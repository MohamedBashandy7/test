<?php

namespace App\Providers;

use App\Models\Projects;
use App\Models\Tasks;
use App\Models\User;
use App\Policies\UsersPolicy;
use App\Policies\ProjectsPolicy;
use App\Policies\TasksPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\ProjectApprovalRequested;
use App\Listeners\Queue\SendProjectApprovalRequestToAdmins;
use App\Events\ProjectApproved;
use App\Listeners\Queue\NotifyProjectManagerAfterApproval;
use App\Events\ProjectRejected;
use App\Listeners\Queue\NotifyProjectManagerAfterRejection;

class AppServiceProvider extends ServiceProvider {
    protected $policies = [
        User::class => UsersPolicy::class,
        Projects::class => ProjectsPolicy::class,
        Tasks::class => TasksPolicy::class,
    ];

    public function register(): void {
    }

    public function boot(): void {
        Gate::policy(Projects::class, ProjectsPolicy::class);
        Gate::policy(User::class, UsersPolicy::class);
        Gate::policy(Tasks::class, TasksPolicy::class);

        Event::listen(ProjectApprovalRequested::class, [SendProjectApprovalRequestToAdmins::class, 'handle']);
        Event::listen(ProjectApproved::class, [NotifyProjectManagerAfterApproval::class, 'handle']);
        Event::listen(ProjectRejected::class, [NotifyProjectManagerAfterRejection::class, 'handle']);
    }
}
