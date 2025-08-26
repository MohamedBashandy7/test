<?php

namespace App\Listeners\Queue;

use App\Events\ProjectApproved;
use App\Notifications\ProjectApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyProjectManagerAfterApproval implements ShouldQueue {
    public function handle(ProjectApproved $event): void {
        $event->project->projectManager?->notify(new ProjectApprovedNotification($event->project));
    }
}