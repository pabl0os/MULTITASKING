<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\ProjectHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $projects = $user->projects()->withCount(['users', 'tasks'])->get();

        // Calculate progress for each project based on completed tasks
        foreach ($projects as $project) {
            $totalTasks = $project->tasks()->count();
            $completedTasks = $project->tasks()->where('status', 'completed')->count();
            $project->progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        }

        return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'global_priority' => 'nullable|integer|min:1|max:5',
            'global_deadline' => 'nullable|date',
            'max_in_process_per_user' => 'nullable|integer|min:1',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'global_priority' => $request->global_priority,
            'global_deadline' => $request->global_deadline,
            'max_in_process_per_user' => $request->max_in_process_per_user ?? 3,
            'status' => 'active',
        ]);

        // RF-11: Creador automáticamente es líder del proyecto
        $project->users()->attach(Auth::id(), ['role' => 'leader']);

        return redirect()->route('projects.show', $project)->with('status', 'Proyecto creado exitosamente.');
    }

    public function show(Project $project)
    {
        $user = Auth::user();
        $pivot = $project->users()->where('user_id', $user->id)->first()?->pivot;
        
        if (!$pivot) {
            abort(403, 'No tienes acceso a este proyecto.');
        }

        $userRole = $pivot->role;

        // Get members and tasks
        $members = $project->users()->withPivot('role')->get();
        
        // We will implement task filtering / sorting in the TaskController, but let's send tasks for now
        $tasks = $project->tasks()->with(['assignee', 'creator'])->get();

        // Current members IDs
        $memberIds = $members->pluck('id')->toArray();

        // History logs (RF-32)
        $histories = $project->histories()->with('user')->orderBy('created_at', 'desc')->get();

        // Calculate progress
        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('status', 'completed')->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        return view('projects.show', compact('project', 'userRole', 'members', 'tasks', 'histories', 'progress'));
    }

    public function update(Request $request, Project $project)
    {
        $user = Auth::user();
        $pivot = $project->users()->where('user_id', $user->id)->first()?->pivot;
        
        if (!$pivot || !in_array($pivot->role, ['leader', 'coleader'])) {
            abort(403, 'No tienes permisos para modificar este proyecto.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'global_priority' => 'nullable|integer|min:1|max:5',
            'global_deadline' => 'nullable|date',
            'max_in_process_per_user' => 'required|integer|min:1',
        ]);

        // RF-22: Modificaciones del límite N (max_in_process_per_user) solo permitidas al líder
        if ($request->max_in_process_per_user != $project->max_in_process_per_user && $pivot->role !== 'leader') {
            return back()->withErrors(['max_in_process_per_user' => 'Solo el líder puede modificar el límite de tareas en proceso por proyecto.']);
        }

        $oldValues = $project->only(['name', 'description', 'global_priority', 'global_deadline', 'max_in_process_per_user']);
        
        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'global_priority' => $request->global_priority,
            'global_deadline' => $request->global_deadline,
            'max_in_process_per_user' => $request->max_in_process_per_user,
        ]);

        // Log history (RF-32)
        ProjectHistory::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'action' => 'project_updated',
            'old_values' => $oldValues,
            'new_values' => $project->only(['name', 'description', 'global_priority', 'global_deadline', 'max_in_process_per_user']),
        ]);

        return redirect()->route('projects.show', $project)->with('status', 'Proyecto actualizado exitosamente.');
    }

    public function destroy(Project $project)
    {
        $user = Auth::user();
        $pivot = $project->users()->where('user_id', $user->id)->first()?->pivot;
        
        // RF-14: El colíder y miembros no pueden borrar el proyecto, sólo el líder
        if (!$pivot || $pivot->role !== 'leader') {
            abort(403, 'Solo el líder puede eliminar el proyecto.');
        }

        $project->delete();

        return redirect()->route('projects')->with('status', 'Proyecto eliminado exitosamente.');
    }

    public function addMember(Request $request, Project $project)
    {
        $user = Auth::user();
        $pivot = $project->users()->where('user_id', $user->id)->first()?->pivot;
        
        if (!$pivot || !in_array($pivot->role, ['leader', 'coleader'])) {
            abort(403, 'No tienes permisos para agregar miembros a este proyecto.');
        }

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:member,coleader',
        ], [
            'email.exists' => 'El usuario con este correo electrónico no existe.',
        ]);

        $userToAdd = User::where('email', $request->email)->first();

        // Check if user is already a member
        if ($project->users()->where('user_id', $userToAdd->id)->exists()) {
             return back()->withErrors(['email' => 'El usuario ya es miembro de este proyecto.']);
        }

        // RF-14: Colíder no puede asignar a miembros como colíderes
        if ($request->role === 'coleader' && $pivot->role !== 'leader') {
            return back()->withErrors(['role' => 'Solo el líder puede asignar el rol de colíder.']);
        }

        $project->users()->attach($userToAdd->id, ['role' => $request->role]);

        // Send a notification? Let's implement that when needed.
        
        return redirect()->route('projects.show', $project)->with('status', 'Miembro agregado exitosamente.');
    }

    public function removeMember(Project $project, User $member)
    {
        $user = Auth::user();
        $pivot = $project->users()->where('user_id', $user->id)->first()?->pivot;
        $memberPivot = $project->users()->where('user_id', $member->id)->first()?->pivot;

        if (!$pivot || !$memberPivot) {
            abort(403, 'Acción no autorizada.');
        }

        // Leader can remove anyone. Coleader can remove members but NOT other coleaders or the leader.
        if ($pivot->role === 'coleader' && in_array($memberPivot->role, ['leader', 'coleader'])) {
            abort(403, 'Un colíder no puede eliminar a otros administradores.');
        }

        if ($pivot->role === 'member') {
            abort(403, 'Los miembros no pueden eliminar participantes.');
        }

        // Set tasks assigned to this member in this project as null (unassigned)
        $project->tasks()->where('assignee_id', $member->id)->update(['assignee_id' => null]);

        $project->users()->detach($member->id);

        return redirect()->route('projects.show', $project)->with('status', 'Miembro removido exitosamente.');
    }

    public function updateMemberRole(Request $request, Project $project, User $member)
    {
        $user = Auth::user();
        $pivot = $project->users()->where('user_id', $user->id)->first()?->pivot;
        $memberPivot = $project->users()->where('user_id', $member->id)->first()?->pivot;

        if (!$pivot || !$memberPivot) {
            abort(403, 'Acción no autorizada.');
        }

        if ($pivot->role !== 'leader') {
            abort(403, 'Solo el líder puede modificar roles de miembros.');
        }

        $request->validate([
            'role' => 'required|in:member,coleader,leader',
        ]);

        if ($request->role === 'leader') {
            // RF-20: Degradación del líder. El líder debe escoger a otro participante para ser el nuevo líder en el proceso.
            // Promotes target to leader, demotes self to coleader (or member)
            $project->users()->updateExistingPivot($member->id, ['role' => 'leader']);
            $project->users()->updateExistingPivot($user->id, ['role' => 'coleader']);
            
            return redirect()->route('projects.show', $project)->with('status', 'Has transferido el liderazgo del proyecto y ahora eres colíder.');
        }

        $project->users()->updateExistingPivot($member->id, ['role' => $request->role]);

        return redirect()->route('projects.show', $project)->with('status', 'Rol actualizado exitosamente.');
    }

    public function completeProject(Project $project)
    {
        $user = Auth::user();
        $pivot = $project->users()->where('user_id', $user->id)->first()?->pivot;

        if (!$pivot || $pivot->role !== 'leader') {
            abort(403, 'Solo el líder puede marcar el proyecto como terminado.');
        }

        // RF-31: Un proyecto no puede ser clasificado como "Terminado" si una tarea activa dentro de él aún está “En proceso”, “Pendiente” o “Retrasada”.
        $activeTasksCount = $project->tasks()->whereIn('status', ['pending', 'in_progress', 'overdue'])->count();
        if ($activeTasksCount > 0) {
            return back()->withErrors(['status' => 'No se puede terminar el proyecto porque hay tareas pendientes, en proceso o atrasadas.']);
        }

        $project->update(['status' => 'completed']);

        // Log history (RF-32)
        ProjectHistory::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'action' => 'project_completed',
        ]);

        return redirect()->route('projects.show', $project)->with('status', 'El proyecto ha sido completado exitosamente.');
    }
}
