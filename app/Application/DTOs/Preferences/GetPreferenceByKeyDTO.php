<?php

namespace App\Application\DTOs\Preferences;

class GetPreferenceByKeyDTO
{
    public function __construct(
        public readonly string $key
    ) {}
} 