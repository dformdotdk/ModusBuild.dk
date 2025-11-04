<?php

namespace App\Domain\Process;

class Gate
{
    public function __construct(
        public string $id,
        public string $projectId,
        public string $phaseId,
        public string $status = 'open',
        public ?string $decidedBy = null,
        public ?\DateTimeImmutable $decidedAt = null,
        public ?string $reason = null
    ) {
    }
}
