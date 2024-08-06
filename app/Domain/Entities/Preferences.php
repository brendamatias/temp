<?php

namespace App\Domain\Entities;

class Preferences
{
    private ?int $id = null;
    private ?int $userId = null;
    private array $options = [];
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->initializeDefaultOptions();
    }

    private function initializeDefaultOptions(): void
    {
        $this->options = [
            'theme' => 'LIGHT',
            'language' => 'pt-BR',
            'currency' => 'BRL',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'notifications_enabled' => true,
            'email_notifications' => true,
            'sms_notifications' => true,
            'mei_annual_limit' => 75000.00,
            'mei_alert_threshold' => 85,
            'mei_monthly_alert_day' => 15
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getOption(string $key): mixed
    {
        return $this->options[$key] ?? null;
    }

    public function setOption(string $key, mixed $value): void
    {
        $this->options[$key] = $value;
        $this->updatedAt = new \DateTime();
    }

    public function getAllOptions(): array
    {
        return $this->options;
    }

    public function setAllOptions(array $options): void
    {
        $this->options = $options;
        $this->updatedAt = new \DateTime();
    }

    public function removeOption(string $key): void
    {
        unset($this->options[$key]);
        $this->updatedAt = new \DateTime();
    }

    public function hasOption(string $key): bool
    {
        return isset($this->options[$key]);
    }

    public function getMeiAnnualLimit(): float
    {
        return $this->options['mei_annual_limit'];
    }

    public function setMeiAnnualLimit(float $limit): void
    {
        $this->options['mei_annual_limit'] = $limit;
        $this->updatedAt = new \DateTime();
    }

    public function getMeiAlertThreshold(): int
    {
        return $this->options['mei_alert_threshold'];
    }

    public function setMeiAlertThreshold(int $threshold): void
    {
        $this->options['mei_alert_threshold'] = $threshold;
        $this->updatedAt = new \DateTime();
    }

    public function getMeiMonthlyAlertDay(): int
    {
        return $this->options['mei_monthly_alert_day'];
    }

    public function setMeiMonthlyAlertDay(int $day): void
    {
        $this->options['mei_monthly_alert_day'] = $day;
        $this->updatedAt = new \DateTime();
    }

    public function isNotificationChannelEnabled(string $channel): bool
    {
        return $this->options[$channel . '_notifications'] ?? false;
    }

    public function setNotificationChannelEnabled(string $channel, bool $enabled): void
    {
        $this->options[$channel . '_notifications'] = $enabled;
        $this->updatedAt = new \DateTime();
    }

    public function getTheme(): string
    {
        return $this->options['theme'];
    }

    public function setTheme(string $theme): void
    {
        $allowedThemes = ['LIGHT', 'DARK'];
        if (!in_array($theme, $allowedThemes, true)) {
            throw new \InvalidArgumentException("Invalid theme: $theme");
        }
        $this->options['theme'] = $theme;
        $this->updatedAt = new \DateTime();
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

} 