<?php

namespace App\Domain\Process;

class ExternalApproval
{
    public function __construct(
        public string $id,
        public string $phaseId,
        public array $approver = [],
        public ?\DateTimeImmutable $approvedAt = null,
        public ?string $tokenHash = null
    ) {
    }
}
