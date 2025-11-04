<?php

namespace App\Domain\Sales;

class Contract
{
    public function __construct(
        public string $id,
        public string $offerId,
        public string $markdown,
        public ?\DateTimeImmutable $signedAt = null,
        public ?array $signer = null
    ) {
    }
}
