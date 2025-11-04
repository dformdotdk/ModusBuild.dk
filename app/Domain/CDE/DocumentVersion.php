<?php

namespace App\Domain\CDE;

class DocumentVersion
{
    public function __construct(
        public string $id,
        public string $documentId,
        public string $revision = 'A',
        public string $version = '1.0',
        public string $storageKey,
        public ?string $checksum,
        public ?int $size,
        public ?string $mime,
        public ?\DateTimeImmutable $approvedAt = null,
        public ?string $approvedBy = null
    ) {
    }
}
