<?php

namespace App\Notifications;

use App\Models\Tasks;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue {
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
            ->subject('New Task Assigned: ' . $this->task->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have been assigned a new task.')
            ->line('**Task:** ' . $this->task->title)
            ->line('**Project:** ' . $this->task->project->name)
            ->line('**Due Date:** ' . $this->task->due_date->format('Y-m-d'))
            ->line('**Description:** ' . $this->task->description)
            ->action('View Task', url('/tasks/' . $this->task->id))
            ->line('Thank you for using our project management system!');
    }

    public function toDatabase($notifiable) {
        return [
            'type' => 'task_assigned',
            'title' => 'New Task Assigned',
            'message' => "You have been assigned to task: {$this->task->title}",
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'due_date' => $this->task->due_date,
            'action_url' => url('/tasks/' . $this->task->id)
        ];
    }

    public function toArray($notifiable) {
        return $this->toDatabase($notifiable);
    }
}