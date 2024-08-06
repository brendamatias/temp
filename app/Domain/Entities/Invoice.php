<?php

namespace App\Domain\Entities;

class Invoice
{
    private ?int $id = null;
    private string $number;
    private float $value;
    private string $serviceDescription;
    private \DateTime $competenceMonth;
    private \DateTime $receiptDate;
    private PartnerCompany $partnerCompany;
    private bool $isActive = true;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt = null;

    public function __construct(
        string $number,
        float $value,
        string $serviceDescription,
        \DateTime $competenceMonth,
        \DateTime $receiptDate,
        PartnerCompany $partnerCompany
    ) {
        $this->number = $number;
        $this->value = $value;
        $this->serviceDescription = $serviceDescription;
        $this->competenceMonth = $competenceMonth;
        $this->receiptDate = $receiptDate;
        $this->partnerCompany = $partnerCompany;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
        $this->updatedAt = new \DateTime();
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): void
    {
        $this->value = $value;
        $this->updatedAt = new \DateTime();
    }

    public function getServiceDescription(): string
    {
        return $this->serviceDescription;
    }

    public function setServiceDescription(string $serviceDescription): void
    {
        $this->serviceDescription = $serviceDescription;
        $this->updatedAt = new \DateTime();
    }

    public function getCompetenceMonth(): \DateTime
    {
        return $this->competenceMonth;
    }

    public function setCompetenceMonth(\DateTime $competenceMonth): void
    {
        $this->competenceMonth = $competenceMonth;
        $this->updatedAt = new \DateTime();
    }

    public function getReceiptDate(): \DateTime
    {
        return $this->receiptDate;
    }

    public function setReceiptDate(\DateTime $receiptDate): void
    {
        $this->receiptDate = $receiptDate;
        $this->updatedAt = new \DateTime();
    }

    public function getPartnerCompany(): PartnerCompany
    {
        return $this->partnerCompany;
    }

    public function setPartnerCompany(PartnerCompany $partnerCompany): void
    {
        $this->partnerCompany = $partnerCompany;
        $this->updatedAt = new \DateTime();
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
} 