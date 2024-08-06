<?php

namespace App\Application\Validators\Preferences;

use App\Application\DTOs\Preferences\GetPreferenceByKeyDTO;

class GetPreferenceByKeyValidator
{
    public function validate(GetPreferenceByKeyDTO $dto): void
    {
        if (empty($dto->key)) {
            throw new \InvalidArgumentException('Chave da preferência é obrigatória');
        }
    }
} 