<?php

namespace App\Application\UseCases\Preferences;

use App\Application\DTOs\Preferences\DeletePreferencesDTO;
use App\Application\Validators\Preferences\DeletePreferencesValidator;
use App\Domain\Repositories\PreferencesRepository;

class DeletePreferencesUseCase
{
    public function __construct(
        private readonly PreferencesRepository $preferencesRepository,
        private readonly DeletePreferencesValidator $validator
    ) {}

    public function execute(DeletePreferencesDTO $dto): void
    {
        $this->validator->validate($dto);
        $this->preferencesRepository->deleteAll();
    }
} 