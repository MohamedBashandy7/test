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
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function projectManager() {
        return $this->belongsTo(User::class, 'project_manager_id');
    }
}
