<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projects extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'projects';
    protected $fillable = [
        'name',
        'description',
        'project_manager_id',
        'status',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function projectManager() {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function approver() {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter() {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
