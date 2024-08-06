<?php

namespace App\Application\UseCases\Preferences;

use App\Application\DTOs\Preferences\DeletePreferenceByKeyDTO;
use App\Application\Validators\Preferences\DeletePreferenceByKeyValidator;
use App\Domain\Repositories\PreferencesRepository;

class DeletePreferenceByKeyUseCase
{
    public function __construct(
        private readonly PreferencesRepository $preferencesRepository,
        private readonly DeletePreferenceByKeyValidator $validator
    ) {}

    public function execute(DeletePreferenceByKeyDTO $dto): void
    {
        $this->validator->validate($dto);
        $this->preferencesRepository->deleteByKey($dto->key);
    }
} 