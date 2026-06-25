<?php

namespace App\Models;

class Access
{
    private ?int $id;
    private int $ownerUserId;
    private int $grantedUserId;
    private string $createdAt;

    public function __construct(int $ownerUserId, int $grantedUserId, ?int $id = null, string $createdAt = '')
    {
        $this->id = $id;
        $this->ownerUserId = $ownerUserId;
        $this->grantedUserId = $grantedUserId;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwnerUserId(): int
    {
        return $this->ownerUserId;
    }

    public function getGrantedUserId(): int
    {
        return $this->grantedUserId;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
