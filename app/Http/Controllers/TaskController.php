<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\ProjectHistory;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Base query: tasks created by or assigned to user
        $query = Task::with(['project', 'assignee', 'creator'])
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('assignee_id', $user->id);
            });

        // Filter by status
        $status = $request->input('status', 'all');
        if (in_array($status, ['pending', 'in_progress', 'completed', 'overdue'])) {
            $query->where('status', $status);
        }

        $tasks = $query->get();

        // Check and update overdue tasks dynamically
        $now = Carbon::now();
        foreach ($tasks as $task) {
            if ($task->status !== 'completed' && $task->deadline && $task->deadline->isPast() && $task->status !== 'overdue') {
                $task->update(['status' => 'overdue']);
                
                // Add notification/log if needed
                if ($task->project_id) {
                    ProjectHistory::create([
                        'project_id' => $task->project_id,
                        'user_id' => $user->id,
                        'task_id' => $task->id,
                        'action' => 'task_updated',
                        'old_values' => ['status' => 'pending'],
                        'new_values' => ['status' => 'overdue'],
                    ]);
                }
            }
        }

        // Sorting
        $sort = $request->input('sort', 'recommended');
        if ($sort === 'priority') {
            $tasks = $tasks->sortByDesc('priority');
        } elseif ($sort === 'date') {
            $tasks = $tasks->sortBy('deadline');
        } else {
            // "Recomendado" algorithm
            // Score = (priority * 24) - hours_remaining
            // Overdue tasks will have negative hours_remaining (making score higher)
            $tasks = $tasks->sortByDesc(function ($task) use ($now) {
                $priority = $task->priority ?? 3;
                if ($task->deadline) {
                    $hoursRemaining = $now->diffInHours($task->deadline, false);
                } else {
                    $hoursRemaining = 72; // Default to 3 days if no deadline
                }
                return ($priority * 24) - $hoursRemaining;
            });
        }

        // Get list of active projects for task creation modal
        $projects = $user->projects()->where('projects.status', 'active')->get();

        return view('tasks.index', compact('tasks', 'projects', 'status', 'sort'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|integer|min:1|max:5',
            'deadline' => 'required|date',
            'project_id' => 'nullable|exists:projects,id',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        // If assigning to project, check permissions
        if ($request->project_id) {
            $project = Project::findOrFail($request->project_id);
            $pivot = $project->users()->where('user_id', Auth::id())->first()?->pivot;
            if (!$pivot || !in_array($pivot->role, ['leader', 'coleader'])) {
                // RF-16: Normal members cannot assign tasks in project
                return back()->withErrors(['assignee_id' => 'Solo el líder o colíder del proyecto pueden crear y asignar tareas.']);
            }
        }

        $task = Task::create([
            'project_id' => $request->project_id,
            'user_id' => Auth::id(),
            'assignee_id' => $request->assignee_id,
            'name' => $request->name,
            'description' => $request->description,
            'priority' => $request->priority,
            'deadline' => $request->deadline,
            'status' => 'pending',
        ]);

        if ($request->project_id) {
            ProjectHistory::create([
                'project_id' => $request->project_id,
                'user_id' => Auth::id(),
                'task_id' => $task->id,
                'action' => 'task_created',
                'new_values' => $task->only(['name', 'priority', 'deadline', 'assignee_id']),
            ]);
        }

        return back()->with('status', 'Tarea creada exitosamente.');
    }

    public function show(Task $task)
    {
        $user = Auth::user();
        
        // Access check
        if ($task->project_id) {
            $pivot = $task->project->users()->where('user_id', $user->id)->first()?->pivot;
            if (!$pivot) {
                abort(403, 'No tienes acceso a este proyecto.');
            }
            $userRole = $pivot->role;
        } else {
            if ($task->user_id !== $user->id && $task->assignee_id !== $user->id) {
                abort(403, 'No tienes acceso a esta tarea.');
            }
            $userRole = 'owner';
        }

        $comments = $task->comments()->with('user')->orderBy('created_at', 'asc')->get();
        $dependencies = $task->dependencies()->get();
        
        // Find other tasks in the same project that can be set as dependency (exclude self and current dependencies)
        $availableDependencies = collect();
        if ($task->project_id) {
            $excludeIds = $dependencies->pluck('id')->merge([$task->id])->toArray();
            $availableDependencies = $task->project->tasks()->whereNotIn('id', $excludeIds)->get();
        }

        return view('tasks.show', compact('task', 'userRole', 'comments', 'dependencies', 'availableDependencies'));
    }

    public function update(Request $request, Task $task)
    {
        $user = Auth::user();
        
        // Access check
        if ($task->project_id) {
            $pivot = $task->project->users()->where('user_id', $user->id)->first()?->pivot;
            if (!$pivot) {
                abort(403, 'No tienes acceso a este proyecto.');
            }
            $userRole = $pivot->role;
        } else {
            if ($task->user_id !== $user->id && $task->assignee_id !== $user->id) {
                abort(403, 'No tienes acceso a esta tarea.');
            }
            $userRole = 'owner';
        }

        // RF-23: Members can only modify tasks assigned to them within a project
        if ($task->project_id && $userRole === 'member' && $task->assignee_id !== $user->id) {
            abort(403, 'Solo puedes modificar tareas asignadas a ti.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|integer|min:1|max:5',
            'deadline' => 'required|date',
            'status' => 'required|in:pending,in_progress,completed,overdue',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        // If status is transitioning to in_progress, check WIP limits (RF-22, RF-23, RF-25)
        if ($request->status === 'in_progress' && $task->status !== 'in_progress') {
            $assigneeId = $request->assignee_id ?? $user->id;
            $assignee = User::findOrFail($assigneeId);

            // RF-23: Personal limit M (default 8)
            $personalLimit = $assignee->max_in_process_tasks;

            // RF-22: Project limit N (default 3)
            $projectLimit = $task->project_id ? $task->project->max_in_process_per_user : null;

            // Global active in-process count
            $globalInProcess = Task::where(function($q) use ($assigneeId) {
                $q->where('assignee_id', $assigneeId)->orWhere(function($sub) use ($assigneeId) {
                    $sub->where('user_id', $assigneeId)->whereNull('assignee_id');
                });
            })->where('status', 'in_progress')->count();

            // Project active in-process count
            $projectInProcess = $task->project_id ? Task::where('project_id', $task->project_id)
                ->where(function($q) use ($assigneeId) {
                    $q->where('assignee_id', $assigneeId)->orWhere(function($sub) use ($assigneeId) {
                        $sub->where('user_id', $assigneeId)->whereNull('assignee_id');
                    });
                })->where('status', 'in_progress')->count() : 0;

            // Rule: Check M (personal limit)
            if ($globalInProcess >= $personalLimit) {
                return back()->withErrors(['status' => "No se puede iniciar la tarea. Se ha alcanzado el límite personal de tareas en proceso (M = {$personalLimit})."]);
            }

            // Rule: Check N (project limit) and RF-25 (most restrictive applies)
            if ($task->project_id) {
                $effectiveLimit = min($personalLimit, $projectLimit);
                if ($projectInProcess >= $effectiveLimit) {
                    return back()->withErrors(['status' => "No se puede iniciar la tarea. Se ha alcanzado el límite permitido en este proyecto (Más restrictivo entre N y M = {$effectiveLimit})."]);
                }
            }
        }

        // RF-9: Serialización. Las tareas posteriores no se deben poder realizar si las anteriores no se han completado.
        if (in_array($request->status, ['in_progress', 'completed']) && $task->status === 'pending') {
            $uncompletedDependencies = $task->dependencies()->where('status', '!=', 'completed')->get();
            if ($uncompletedDependencies->isNotEmpty()) {
                $names = $uncompletedDependencies->pluck('name')->implode(', ');
                return back()->withErrors(['status' => "Esta tarea está bloqueada. Debes completar primero las tareas anteriores: {$names}."]);
            }
        }

        $oldValues = $task->only(['name', 'description', 'priority', 'deadline', 'status', 'assignee_id']);
        
        $task->update([
            'name' => $request->name,
            'description' => $request->description,
            'priority' => $request->priority,
            'deadline' => $request->deadline,
            'status' => $request->status,
            'assignee_id' => $request->assignee_id,
        ]);

        if ($task->project_id) {
            ProjectHistory::create([
                'project_id' => $task->project_id,
                'user_id' => $user->id,
                'task_id' => $task->id,
                'action' => 'task_updated',
                'old_values' => $oldValues,
                'new_values' => $task->only(['name', 'description', 'priority', 'deadline', 'status', 'assignee_id']),
            ]);
        }

        // RF-30: Si una tarea predecesora se marca como "Completada", notificar a los encargados de las sucesoras.
        if ($task->status === 'completed' && $oldValues['status'] !== 'completed') {
            // Log history
            if ($task->project_id) {
                ProjectHistory::create([
                    'project_id' => $task->project_id,
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'action' => 'task_completed',
                ]);
            }

            // Find dependents
            $dependents = $task->dependents()->get();
            foreach ($dependents as $dep) {
                // If all dependencies of the dependent are now completed, notify!
                $stillBlocked = $dep->dependencies()->where('status', '!=', 'completed')->exists();
                if (!$stillBlocked && $dep->assignee_id) {
                    // Send Laravel Database Notification
                    $assigneeUser = User::find($dep->assignee_id);
                    if ($assigneeUser) {
                        $assigneeUser->notify(new \App\Notifications\TaskUnlockedNotification($dep));
                    }
                }
            }
        }

        return redirect()->route('tasks.show', $task)->with('status', 'Tarea actualizada exitosamente.');
    }

    public function destroy(Task $task)
    {
        $user = Auth::user();

        // Access check
        if ($task->project_id) {
            $pivot = $task->project->users()->where('user_id', $user->id)->first()?->pivot;
            if (!$pivot || !in_array($pivot->role, ['leader', 'coleader'])) {
                abort(403, 'Solo el líder o colíder del proyecto pueden eliminar tareas.');
            }
        } else {
            if ($task->user_id !== $user->id) {
                abort(403, 'Solo puedes eliminar tus propias tareas.');
            }
        }

        // RF-10: Re-serialización. Si una tarea serializada se borra, las que seguían dejan de estar serializadas
        // con la que se borró y pasan a poder realizarse. Enviamos notificación.
        $dependents = $task->dependents()->get();
        
        $projectId = $task->project_id;
        $taskName = $task->name;

        // Perform delete (will cascade delete dependency rows)
        $task->delete();

        // Notify dependents if they are no longer blocked
        foreach ($dependents as $dep) {
            $stillBlocked = $dep->dependencies()->where('status', '!=', 'completed')->exists();
            if (!$stillBlocked && $dep->assignee_id) {
                $assigneeUser = User::find($dep->assignee_id);
                if ($assigneeUser) {
                    $assigneeUser->notify(new \App\Notifications\TaskUnlockedNotification($dep, $taskName));
                }
            }
        }

        if ($projectId) {
            ProjectHistory::create([
                'project_id' => $projectId,
                'user_id' => $user->id,
                'action' => 'task_deleted',
                'old_values' => ['name' => $taskName],
            ]);
            return redirect()->route('projects.show', $projectId)->with('status', 'Tarea eliminada exitosamente.');
        }

        return redirect()->route('tasks')->with('status', 'Tarea eliminada exitosamente.');
    }

    public function addDependency(Request $request, Task $task)
    {
        $request->validate([
            'depends_on_task_id' => 'required|exists:tasks,id',
        ]);

        if ($task->id == $request->depends_on_task_id) {
            return back()->withErrors(['dependency' => 'Una tarea no puede depender de sí misma.']);
        }

        // Attach dependency
        $task->dependencies()->attach($request->depends_on_task_id);

        return back()->with('status', 'Dependencia añadida exitosamente.');
    }

    public function removeDependency(Task $task, Task $dependency)
    {
        $task->dependencies()->detach($dependency->id);
        return back()->with('status', 'Dependencia removida exitosamente.');
    }

    public function addComment(Request $request, Task $task)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        Comment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return back()->with('status', 'Comentario añadido exitosamente.');
    }
}
