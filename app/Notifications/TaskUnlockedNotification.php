<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskUnlockedNotification extends Notification
{
    use Queueable;

    protected $task;
    protected $deletedTaskName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, $deletedTaskName = null)
    {
        $this->task = $task;
        $this->deletedTaskName = $deletedTaskName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->deletedTaskName) {
            $message = "La tarea '{$this->deletedTaskName}' de la que dependías fue eliminada. Ahora puedes iniciar '{$this->task->name}'.";
        } else {
            $message = "La tarea anterior fue completada. Ya puedes iniciar tu tarea: '{$this->task->name}'.";
        }

        return [
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'message' => $message,
            'type' => 'task_unlocked',
        ];
    }
}
