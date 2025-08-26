<?php

namespace App\Notifications;

use App\Models\Projects;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectApprovedNotification extends Notification implements ShouldQueue {
    use Queueable;

    public function __construct(private readonly Projects $project) {
    }

    public function via(object $notifiable): array {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage {
        return (new MailMessage)
            ->subject('Project Approved')
            ->line('Your project has been approved: ' . ($this->project->name ?? ('#' . $this->project->id)));
    }
}