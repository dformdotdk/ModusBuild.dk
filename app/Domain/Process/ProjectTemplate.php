<?php

namespace App\Domain\Process;

class ProjectTemplate
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $name,
        public array $structure
    ) {
    }
}
