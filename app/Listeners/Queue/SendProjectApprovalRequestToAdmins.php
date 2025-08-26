<?php

namespace App\Listeners\Queue;

use App\Events\ProjectApprovalRequested;
use App\Models\User;
use App\Notifications\ProjectApprovalRequestNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendProjectApprovalRequestToAdmins implements ShouldQueue {
    public function handle(ProjectApprovalRequested $event): void {
        $admins = User::query()->where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new ProjectApprovalRequestNotification($event->project, $event->action));
        }
    }
}