<?php

namespace App\Application\DTOs\Preferences;

class DeletePreferenceByKeyDTO
{
    public function __construct(
        public readonly string $key
    ) {}
} 