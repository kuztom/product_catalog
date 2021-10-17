<?php

namespace App\Models;

class Product
{
    private string $id;
    private string $title;
    private string $category;
    private int $qty;
    private string $createdAt;
    private string $createdBy;
    private string $editedAt;

    public function __construct(
        string  $id,
        string  $title,
        string  $category,
        int     $qty,
        ?string $createdAt,
        ?string $createdBy,
        ?string $editedAt,
    )
    {
        $this->id = $id;
        $this->title = $title;
        $this->category = $category;
        $this->qty = $qty;
        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
        $this->editedAt = $editedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function getEditedAt(): string
    {
        return $this->editedAt;
    }
}