<?php

namespace App\Domain\CDE;

class Document
{
    public function __construct(
        public string $id,
        public string $projectId,
        public string $code,
        public string $title,
        public ?string $discipline,
        public ?string $phaseCode,
        public array $classification = [],
        public string $status = 'draft',
        public ?string $currentVersionId = null,
        public int $lockedVersion = 0
    ) {
    }
}
