<?php

namespace App\Models;

class User
{
    private ?int $id;
    private string $login;
    private string $passwordHash;
    private string $createdAt;

    public function __construct(string $login, string $passwordHash, ?int $id = null, string $createdAt = '')
    {
        $this->id = $id;
        $this->login = $login;
        $this->passwordHash = $passwordHash;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
