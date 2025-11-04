<?php

namespace App\Domain\Process;

class Phase
{
    public function __construct(
        public string $id,
        public string $projectId,
        public string $code,
        public string $name,
        public string $status = 'active',
        public ?\DateTimeImmutable $startsAt = null,
        public ?\DateTimeImmutable $endsAt = null
    ) {
    }
}
