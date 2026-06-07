<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskOverdueNotification extends Notification
{
    use Queueable;

    protected $task;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
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
        $message = "¡ALERTA! La tarea <strong>'{$this->task->name}'</strong> se encuentra atrasada. Por favor complétala.";

        return [
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'message' => $message,
            'type' => 'task_overdue',
        ];
    }
}
