<?php

namespace App\Events;

use App\Models\Projects;

class ProjectApprovalRequested {
    public function __construct(
        public Projects $project,
        public string $action // 'created' | 'updated'
    ) {
    }
}