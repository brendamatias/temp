<?php

namespace App\Application\UseCases\Preferences;

use App\Application\DTOs\Preferences\ActivatePreferencesDTO;
use App\Application\Validators\Preferences\ActivatePreferencesValidator;
use App\Domain\Repositories\PreferencesRepository;

class ActivatePreferencesUseCase
{
    public function __construct(
        private readonly PreferencesRepository $preferencesRepository,
        private readonly ActivatePreferencesValidator $validator
    ) {}

    public function execute(ActivatePreferencesDTO $dto): void
    {
        $this->validator->validate($dto);
        
        $preferences = $this->preferencesRepository->find();
        if (!$preferences) {
            throw new \InvalidArgumentException('Preferências não encontradas');
        }

        if ($preferences->isActive()) {
            throw new \InvalidArgumentException('Preferências já estão ativas');
        }

        $preferences->setIsActive(true);
        $this->preferencesRepository->save($preferences);
    }
} 