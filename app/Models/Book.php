<?php

namespace App\Models;

class Book
{
    private ?int $id;
    private int $userId;
    private string $title;
    private string $content;
    private string $createdAt;
    private string $updatedAt;
    private ?string $deletedAt;

    public function __construct(
        int $userId,
        string $title,
        string $content,
        ?int $id = null,
        string $createdAt = '',
        string $updatedAt = '',
        ?string $deletedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deletedAt = $deletedAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?string
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
