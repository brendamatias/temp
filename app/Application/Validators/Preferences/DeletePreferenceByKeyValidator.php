<?php

namespace App\Application\Validators\Preferences;

use App\Application\DTOs\Preferences\DeletePreferenceByKeyDTO;

class DeletePreferenceByKeyValidator
{
    public function validate(DeletePreferenceByKeyDTO $dto): void
    {
        if (empty($dto->key)) {
            throw new \InvalidArgumentException('Chave da preferência é obrigatória');
        }
    }
} 