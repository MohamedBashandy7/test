<?php

namespace App\Events;

use App\Models\Projects;

class ProjectApproved {
    public function __construct(public Projects $project) {
    }
}