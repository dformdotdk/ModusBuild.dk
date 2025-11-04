<?php

namespace App\Domain\CDE;

class Transmittal
{
    public function __construct(
        public string $id,
        public string $projectId,
        public string $number,
        public array $recipients = [],
        public ?\DateTimeImmutable $sentAt = null,
        public array $receiptLog = []
    ) {
    }
}
