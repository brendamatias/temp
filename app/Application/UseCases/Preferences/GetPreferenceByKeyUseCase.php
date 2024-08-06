<?php

namespace App\Application\UseCases\Preferences;

use App\Application\DTOs\Preferences\GetPreferenceByKeyDTO;
use App\Application\Validators\Preferences\GetPreferenceByKeyValidator;
use App\Domain\Repositories\PreferencesRepository;
use App\Domain\Entities\Preferences;

class GetPreferenceByKeyUseCase
{
    public function __construct(
        private readonly PreferencesRepository $preferencesRepository,
        private readonly GetPreferenceByKeyValidator $validator
    ) {}

    public function execute(GetPreferenceByKeyDTO $dto): ?Preferences
    {
        $this->validator->validate($dto);
        $preference = $this->preferencesRepository->findByKey($dto->key);
        
        if (!$preference) {
            return null;
        }

        return $preference;
    }
} 