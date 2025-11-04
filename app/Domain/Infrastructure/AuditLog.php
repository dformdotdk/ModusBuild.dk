<?php

namespace App\Domain\Infrastructure;

class AuditLog
{
    public function __construct(
        public int $id,
        public string $actorId,
        public string $action,
        public string $targetType,
        public string $targetId,
        public \DateTimeImmutable $timestamp,
        public array $meta = []
    ) {
    }
}
