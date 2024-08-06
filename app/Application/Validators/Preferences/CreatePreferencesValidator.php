<?php

namespace App\Application\Validators\Preferences;

use App\Application\DTOs\Preferences\CreatePreferencesDTO;

class CreatePreferencesValidator
{
    public function validate(CreatePreferencesDTO $dto): void
    {
        if (empty($dto->options)) {
            throw new \InvalidArgumentException('As opções de preferências são obrigatórias');
        }

        foreach ($dto->options as $key => $value) {
            if (empty($key)) {
                throw new \InvalidArgumentException('A chave da preferência não pode ser vazia');
            }
        }
    }
} 