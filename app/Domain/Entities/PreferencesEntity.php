<?php

namespace App\Domain\Entities;

use DateTimeImmutable;

class PreferencesEntity
{
    private array $options = [];

    public function __construct(
        private readonly int $id,
        private readonly DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt = null
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setOption(string $key, mixed $value): void
    {
        $this->options[$key] = $value;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getOption(string $key): mixed
    {
        return $this->options[$key] ?? null;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
} 