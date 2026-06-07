<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskDeadlineWarningNotification extends Notification
{
    use Queueable;

    protected $task;
    protected $alertType;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, string $alertType)
    {
        $this->task = $task;
        $this->alertType = $alertType;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $timeString = $this->task->deadline ? $this->task->deadline->format('H:i d/m/Y') : '';
        if ($this->alertType === 'same_day') {
            $message = "La tarea <strong>'{$this->task->name}'</strong> vence hoy a las {$timeString}. ¡Trabaja en ella!";
        } else {
            $message = "La tarea <strong>'{$this->task->name}'</strong> vence pronto ({$timeString}).";
        }

        return [
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'message' => $message,
            'type' => 'task_warning',
        ];
    }
}
