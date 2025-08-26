<?php

namespace App\Listeners\Queue;

use App\Events\ProjectRejected;
use App\Notifications\ProjectRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyProjectManagerAfterRejection implements ShouldQueue {
    public function handle(ProjectRejected $event): void {
        $event->project->projectManager?->notify(new ProjectRejectedNotification($event->project));
    }
}
