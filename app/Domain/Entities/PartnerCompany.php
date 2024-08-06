<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Document;

class PartnerCompany
{
    private ?int $id;
    private string $name;
    private string $legalName;
    private Document $document;
    private bool $isActive;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt = null;

    public function __construct(
        ?int $id,
        string $name,
        string $legalName,
        string $document
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->legalName = $legalName;
        $this->document = new Document($document);
        $this->isActive = true;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTime();
    }

    public function getLegalName(): string
    {
        return $this->legalName;
    }

    public function setLegalName(string $legalName): void
    {
        $this->legalName = $legalName;
        $this->updatedAt = new \DateTime();
    }

    public function getDocument(): string
    {
        return $this->document->getValue();
    }

    public function getFormattedDocument(): string
    {
        return $this->document->getFormatted();
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTime();
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'legal_name' => $this->legalName,
            'document' => $this->getFormattedDocument(),
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null
        ];
    }
} 