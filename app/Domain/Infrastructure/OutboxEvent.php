<?php

namespace App\Domain\Infrastructure;

class OutboxEvent
{
    public function __construct(
        public int $id,
        public string $eventType,
        public array $payload,
        public string $dedupeKey,
        public ?\DateTimeImmutable $publishedAt = null
    ) {
    }
}
