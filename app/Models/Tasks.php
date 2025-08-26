<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Projects;

class Tasks extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'assigned_to',
        'project_id',
        'due_date',
        'attachment_path'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function assignee() {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function updater() {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function project() {
        return $this->belongsTo(Projects::class);
    }

    protected static function boot() {
        parent::boot();

        static::created(function ($task) {
            if ($task->assigned_user_id) {
                $task->assignedUser->notify(new \App\Notifications\TaskAssignedNotification($task));
            }
        });

        static::updated(function ($task) {
            if ($task->isDirty('assigned_user_id') && $task->assigned_user_id) {
                $task->assignedUser->notify(new \App\Notifications\TaskAssignedNotification($task));
            }
        });
    }
}
