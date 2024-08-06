<?php

namespace App\Domain\ValueObjects;

class Document
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $this->format($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getFormatted(): string
    {
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $this->value);
    }

    private function format(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
} 