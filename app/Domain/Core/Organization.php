<?php

namespace App\Domain\Core;

class Organization
{
    /**
     * @param array<int, string> $projectCodes
     */
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $name,
        public string $slug,
        public array $projectCodes = []
    ) {
    }
}
