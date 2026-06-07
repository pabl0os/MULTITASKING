<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function destroyAccount(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Delete tasks owned by the user that are NOT in a project?
        // Wait, RF-33 says "El sistema debe borrar las tareas de un usuario cuando este elimine su cuenta, asimismo las tareas asignadas a este dentro de un proyecto deben ser marcadas como sin asignar y en caso de que el usuario esté solo en un proyecto (Como líder) este también deberá ser borrado."
        // So tasks created by him (ownedTasks) are deleted. If they were in a project, they get deleted too.
        $user->ownedTasks()->delete();

        // 2. Tasks assigned to him inside a project should be set to assignee_id = null (unassigned)
        \App\Models\Task::where('assignee_id', $user->id)->update(['assignee_id' => null]);

        // 3. Handle leader transition and project deletion
        $projects = $user->projects;
        foreach ($projects as $project) {
            if ($project->pivot->role === 'leader') {
                // Get other users in the project
                $otherMembers = $project->users()->where('user_id', '!=', $user->id)->get();
                
                if ($otherMembers->isEmpty()) {
                    // If he is the only user in the project, delete it
                    $project->delete();
                } else {
                    // Try to find the co-leader with highest seniority (oldest created_at in pivot table)
                    $newLeaderPivot = \DB::table('project_user')
                        ->where('project_id', $project->id)
                        ->where('user_id', '!=', $user->id)
                        ->where('role', 'coleader')
                        ->orderBy('created_at', 'asc')
                        ->first();

                    if (!$newLeaderPivot) {
                        // If no co-leader, find oldest member
                        $newLeaderPivot = \DB::table('project_user')
                            ->where('project_id', $project->id)
                            ->where('user_id', '!=', $user->id)
                            ->where('role', 'member')
                            ->orderBy('created_at', 'asc')
                            ->first();
                    }

                    if ($newLeaderPivot) {
                        // Update role to leader
                        \DB::table('project_user')
                            ->where('project_id', $project->id)
                            ->where('user_id', $newLeaderPivot->user_id)
                            ->update(['role' => 'leader']);
                    }
                }
            }
        }

        // 4. Logout and delete the user
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Tu cuenta ha sido eliminada con éxito.');
    }
}
