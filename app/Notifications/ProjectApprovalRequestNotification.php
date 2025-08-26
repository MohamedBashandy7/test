<?php

namespace App\Notifications;

use App\Models\Projects;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectApprovalRequestNotification extends Notification implements ShouldQueue {
    use Queueable;

    public function __construct(
        private readonly Projects $project,
        private readonly string $action // 'created' | 'updated'
    ) {
    }

    public function via(object $notifiable): array {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage {
        return (new MailMessage)
            ->subject('Project ' . ($this->action === 'created' ? 'Creation' : 'Update') . ' Approval Requested')
            ->line('Project: ' . ($this->project->name ?? ('#' . $this->project->id)))
            ->line('Action: ' . $this->action)
            ->line('Please review and approve.');
    }
}