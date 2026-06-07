<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\User;
use App\Models\ProjectHistory;
use App\Notifications\TaskDeadlineWarningNotification;
use App\Notifications\TaskOverdueNotification;
use Carbon\Carbon;

class CheckTaskDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica plazos de tareas y envía alertas correspondientes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info("Iniciando verificación de plazos: {$now}");

        // Fetch all active tasks
        $tasks = Task::where('status', '!=', 'completed')->get();

        foreach ($tasks as $task) {
            $user = User::find($task->assignee_id ?? $task->user_id);
            if (!$user) {
                continue;
            }

            // 1. Check if overdue
            if ($task->deadline && $task->deadline->isPast()) {
                if ($task->status !== 'overdue') {
                    $oldStatus = $task->status;
                    $task->update(['status' => 'overdue']);
                    $this->info("Tarea #{$task->id} ('{$task->name}') marcada como atrasada.");

                    // Log project history
                    if ($task->project_id) {
                        ProjectHistory::create([
                            'project_id' => $task->project_id,
                            'user_id' => $user->id,
                            'task_id' => $task->id,
                            'action' => 'task_updated',
                            'old_values' => ['status' => $oldStatus],
                            'new_values' => ['status' => 'overdue'],
                        ]);
                    }

                    // Check if overdue notification already sent
                    $alreadyNotified = \DB::table('notifications')
                        ->where('notifiable_id', $user->id)
                        ->where('data', 'like', '%"task_id":' . $task->id . '%')
                        ->where('data', 'like', '%"type":"task_overdue"%')
                        ->exists();

                    if (!$alreadyNotified) {
                        $user->notify(new TaskOverdueNotification($task));
                        $this->info("Notificación de atraso enviada al usuario #{$user->id}");
                    }
                }
                continue;
            }

            // 2. Check warning alerts (RF-8, RF-11)
            if ($task->deadline) {
                $createdDate = $task->created_at ?? $now;
                $isSameDay = $createdDate->isSameDay($task->deadline);

                $shouldNotify = false;
                $alertType = '';

                if ($isSameDay) {
                    // Same day notification window (notify_sameday_hours before deadline)
                    $hoursBefore = $user->notify_sameday_hours ?? 2;
                    $notifyTime = $task->deadline->copy()->subHours($hoursBefore);
                    
                    if ($now->greaterThanOrEqualTo($notifyTime)) {
                        $shouldNotify = true;
                        $alertType = 'same_day';
                    }
                } else {
                    // Different day notification window (notify_diffday_days before deadline)
                    $daysBefore = $user->notify_diffday_days ?? 2;
                    $notifyTime = $task->deadline->copy()->subDays($daysBefore);

                    if ($now->greaterThanOrEqualTo($notifyTime)) {
                        $shouldNotify = true;
                        $alertType = 'diff_day';
                    }
                }

                if ($shouldNotify) {
                    // Verify if warning notification was already sent to avoid spam
                    $alreadyNotified = \DB::table('notifications')
                        ->where('notifiable_id', $user->id)
                        ->where('data', 'like', '%"task_id":' . $task->id . '%')
                        ->where('data', 'like', '%"type":"task_warning"%')
                        ->exists();

                    if (!$alreadyNotified) {
                        $user->notify(new TaskDeadlineWarningNotification($task, $alertType));
                        $this->info("Alerta de proximidad ({$alertType}) enviada al usuario #{$user->id} para tarea #{$task->id}");
                    }
                }
            }
        }

        $this->info("Verificación de plazos completada.");
    }
}
