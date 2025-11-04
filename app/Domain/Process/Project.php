<?php

namespace App\Domain\Process;

class Project
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $organizationId,
        public string $templateId,
        public string $status = 'active',
        public string $code
    ) {
    }
}
