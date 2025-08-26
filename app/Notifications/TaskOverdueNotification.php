<?php

namespace App\Notifications;

use App\Models\Tasks;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskOverdueNotification extends Notification implements ShouldQueue {
    use Queueable;

    protected $task;

    public function __construct(Tasks $task) {
        $this->task = $task;
    }

    public function via($notifiable) {
        return ['mail', 'database'];
    }

    public function toMail($notifiable) {
        return (new MailMessage)
            ->subject('Overdue Task: ' . $this->task->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have an overdue task that requires attention.')
            ->line('**Task:** ' . $this->task->title)
            ->line('**Project:** ' . $this->task->project->name)
            ->line('**Due Date:** ' . $this->task->due_date->format('Y-m-d'))
            ->line('**Days Overdue:** ' . $this->task->due_date->diffInDays(now()))
            ->action('View Task', url('/tasks/' . $this->task->id))
            ->line('Please update the task status as soon as possible.');
    }

    public function toDatabase($notifiable) {
        return [
            'type' => 'task_overdue',
            'title' => 'Task Overdue',
            'message' => "Task '{$this->task->title}' is overdue",
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'due_date' => $this->task->due_date,
            'days_overdue' => $this->task->due_date->diffInDays(now()),
            'action_url' => url('/tasks/' . $this->task->id)
        ];
    }

    public function toArray($notifiable) {
        return $this->toDatabase($notifiable);
    }
}