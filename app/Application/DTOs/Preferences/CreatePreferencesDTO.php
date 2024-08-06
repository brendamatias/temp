<?php

namespace App\Application\DTOs\Preferences;

class CreatePreferencesDTO
{
    public function __construct(
        public readonly int $user_id,
        public readonly array $options
    ) {}
} 