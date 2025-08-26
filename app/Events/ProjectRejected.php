<?php

namespace App\Events;

use App\Models\Projects;

class ProjectRejected {
    public function __construct(public Projects $project) {
    }
}
