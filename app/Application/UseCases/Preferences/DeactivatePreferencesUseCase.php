<?php

namespace App\Application\UseCases\Preferences;

use App\Application\DTOs\Preferences\DeactivatePreferencesDTO;
use App\Application\Validators\Preferences\DeactivatePreferencesValidator;
use App\Domain\Repositories\PreferencesRepository;

class DeactivatePreferencesUseCase
{
    public function __construct(
        private readonly PreferencesRepository $preferencesRepository,
        private readonly DeactivatePreferencesValidator $validator
    ) {}

    public function execute(DeactivatePreferencesDTO $dto): void
    {
        $this->validator->validate($dto);
        
        $preferences = $this->preferencesRepository->find();
        if (!$preferences) {
            throw new \InvalidArgumentException('Preferências não encontradas');
        }

        if (!$preferences->isActive()) {
            throw new \InvalidArgumentException('Preferências já estão inativas');
        }

        $preferences->setIsActive(false);
        $this->preferencesRepository->save($preferences);
    }
} 