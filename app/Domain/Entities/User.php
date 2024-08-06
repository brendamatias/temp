<?php

namespace App\Domain\Entities;

class User
{
    private int $id;
    private string $name;
    private string $email;
    private ?string $phone;
    private string $password;
    private ?string $emailVerifiedAt;
    private ?string $rememberToken;

    public function __construct(
        string $name,
        string $email,
        string $password,
        ?string $phone = null,
        ?string $emailVerifiedAt = null,
        ?string $rememberToken = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->password = $password;
        $this->emailVerifiedAt = $emailVerifiedAt;
        $this->rememberToken = $rememberToken;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmailVerifiedAt(): ?string
    {
        return $this->emailVerifiedAt;
    }

    public function getRememberToken(): ?string
    {
        return $this->rememberToken;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setEmailVerifiedAt(?string $emailVerifiedAt): void
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
    }

    public function setRememberToken(?string $rememberToken): void
    {
        $this->rememberToken = $rememberToken;
    }
} 