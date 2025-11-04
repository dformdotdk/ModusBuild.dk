<?php

namespace App\Domain\Core;

use Illuminate\Support\Str;

class Tenant
{
    public function __construct(
        public string $id,
        public string $name,
        public string $planId,
    ) {
    }

    public static function create(string $name, string $planId): self
    {
        return new self(Str::uuid()->toString(), $name, $planId);
    }
}
