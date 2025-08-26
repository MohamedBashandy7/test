<?php

namespace App\Notifications;

use App\Models\Projects;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectStatusNotification extends Notification implements ShouldQueue {
    use Queueable;

    protected $project;
    protected $status; // 'approved' or 'rejected'
    protected $message;

    public function __construct(Projects $project, $status, $message = null) {
        $this->project = $project;
        $this->status = $status;
        $this->message = $message;
    }

    public function via($notifiable) {
        return ['mail', 'database'];
    }

    public function toMail($notifiable) {
        $subject = ucfirst($this->status) . ': ' . $this->project->name;
        $statusText = $this->status === 'approved' ? 'approved' : 'rejected';

        $mail = (new MailMessage)
            ->subject('Project ' . $subject)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line("Your project '{$this->project->name}' has been {$statusText}.");

        if ($this->message) {
            $mail->line('**Admin Message:** ' . $this->message);
        }

        return $mail->action('View Project', url('/projects/' . $this->project->id))
            ->line('Thank you for using our project management system!');
    }

    public function toDatabase($notifiable) {
        return [
            'type' => 'project_' . $this->status,
            'title' => 'Project ' . ucfirst($this->status),
            'message' => "Project '{$this->project->name}' has been {$this->status}",
            'project_id' => $this->project->id,
            'admin_message' => $this->message,
            'action_url' => url('/projects/' . $this->project->id)
        ];
    }

    public function toArray($notifiable) {
        return $this->toDatabase($notifiable);
    }
}