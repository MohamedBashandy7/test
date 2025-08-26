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
    }
}