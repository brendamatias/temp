<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Preferences;

interface PreferencesRepository
{
    public function save(Preferences $preferences): void;
    public function find(int $userId): ?Preferences;
    public function findById(int $id): ?Preferences;
    public function findByKey(string $key): ?Preferences;
    public function findAll(): array;
    public function updateOption(string $key, mixed $value): void;
    public function getOption(string $key): mixed;
    public function getAllOptions(): array;
} 