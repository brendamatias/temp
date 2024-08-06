<?php

namespace App\Domain\Entities;

class Expense
{
    private ?int $id = null;
    private string $name;
    private float $value;
    private int $categoryId;
    private \DateTime $paymentDate;
    private \DateTime $competenceDate;
    private ?int $partnerCompanyId = null;
    private ?int $invoiceId = null;
    private bool $isActive = true;
    private bool $isPaid = false;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt = null;

    public function __construct(
        string $name,
        float $value,
        int $categoryId,
        \DateTime $paymentDate,
        \DateTime $competenceDate,
        ?int $partnerCompanyId = null,
        ?int $invoiceId = null,
        bool $isPaid = false
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->categoryId = $categoryId;
        $this->paymentDate = $paymentDate;
        $this->competenceDate = $competenceDate;
        $this->partnerCompanyId = $partnerCompanyId;
        $this->invoiceId = $invoiceId;
        $this->isPaid = $isPaid;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): void
    {
        $this->value = $value;
        $this->updatedAt = new \DateTime();
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): void
    {
        $this->categoryId = $categoryId;
        $this->updatedAt = new \DateTime();
    }

    public function getPaymentDate(): \DateTime
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTime $paymentDate): void
    {
        $this->paymentDate = $paymentDate;
        $this->updatedAt = new \DateTime();
    }

    public function getCompetenceDate(): \DateTime
    {
        return $this->competenceDate;
    }

    public function setCompetenceDate(\DateTime $competenceDate): void
    {
        $this->competenceDate = $competenceDate;
        $this->updatedAt = new \DateTime();
    }

    public function getPartnerCompanyId(): ?int
    {
        return $this->partnerCompanyId;
    }

    public function setPartnerCompanyId(?int $partnerCompanyId): void
    {
        $this->partnerCompanyId = $partnerCompanyId;
        $this->updatedAt = new \DateTime();
    }

    public function getInvoiceId(): ?int
    {
        return $this->invoiceId;
    }

    public function setInvoiceId(?int $invoiceId): void
    {
        $this->invoiceId = $invoiceId;
        $this->updatedAt = new \DateTime();
    }

    public function isPaid(): bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): void
    {
        $this->isPaid = $isPaid;
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

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTime();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
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
} 