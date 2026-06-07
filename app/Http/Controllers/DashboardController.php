<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Stats
        $pendingCount = Task::where(function($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('assignee_id', $user->id);
            })
            ->whereIn('status', ['pending', 'overdue'])
            ->count();

        $inProgressCount = Task::where(function($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('assignee_id', $user->id);
            })
            ->where('status', 'in_progress')
            ->count();

        $completedTodayCount = Task::where(function($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('assignee_id', $user->id);
            })
            ->where('status', 'completed')
            ->whereDate('updated_at', Carbon::today())
            ->count();

        // Prioritized Tasks (Recommended order)
        // Top 5 tasks that are pending, in_progress, or overdue
        $tasks = Task::with(['project', 'assignee', 'creator'])
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('assignee_id', $user->id);
            })
            ->whereIn('status', ['pending', 'in_progress', 'overdue'])
            ->get();

        $now = Carbon::now();
        $prioritizedTasks = $tasks->sortByDesc(function ($task) use ($now) {
            $priority = $task->priority ?? 3;
            if ($task->deadline) {
                $hoursRemaining = $now->diffInHours($task->deadline, false);
            } else {
                $hoursRemaining = 72;
            }
            return ($priority * 24) - $hoursRemaining;
        })->take(5);

        return view('dashboard.index', compact('pendingCount', 'inProgressCount', 'completedTodayCount', 'prioritizedTasks'));
    }
}
