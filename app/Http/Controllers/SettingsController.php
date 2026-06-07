<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'max_in_process_tasks' => 'required|integer|min:1|max:100',
            'notify_sameday_hours' => 'required|integer|min:1|max:24',
            'notify_diffday_days' => 'required|integer|min:1|max:30',
        ]);

        $user->update([
            'max_in_process_tasks' => $request->max_in_process_tasks,
            'notify_sameday_hours' => $request->notify_sameday_hours,
            'notify_diffday_days' => $request->notify_diffday_days,
        ]);

        return redirect()->route('settings')->with('status', 'Configuración actualizada exitosamente.');
    }
}
