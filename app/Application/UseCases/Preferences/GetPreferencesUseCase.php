<?php

namespace App\Application\UseCases\Preferences;

use App\Application\DTOs\Preferences\GetPreferencesDTO;
use App\Application\Validators\Preferences\GetPreferencesValidator;
use App\Domain\Repositories\PreferencesRepository;

class GetPreferencesUseCase
{
    public function __construct(
        private readonly PreferencesRepository $preferencesRepository,
        private readonly GetPreferencesValidator $validator
    ) {}

    public function execute(GetPreferencesDTO $dto): array
    {
        $this->validator->validate($dto);
        return $this->preferencesRepository->findAll();
    }
} 