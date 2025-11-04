<?php

namespace App\Domain\CDE;

class Workflow
{
    public function __construct(
        public string $id,
        public string $projectId,
        public string $name,
        public array $steps = []
    ) {
    }
}
