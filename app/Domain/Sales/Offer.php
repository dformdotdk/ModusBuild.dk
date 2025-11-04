<?php

namespace App\Domain\Sales;

class Offer
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $organizationId,
        public array $projectSeed,
        public float $total,
        public string $status = 'draft',
        public ?\DateTimeImmutable $sentAt = null,
        public ?\DateTimeImmutable $acceptedAt = null
    ) {
    }
}
