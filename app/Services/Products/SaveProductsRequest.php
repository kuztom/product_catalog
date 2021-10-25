<?php

namespace App\Services\Products;

class SaveProductsRequest
{
    private string $title;
    private string $categoryOption;
    private int $qty;
    private string $createdBy;
    private array $tags;

    public function __construct(string $title, string $categoryOption, int $qty, string $createdBy, ?array $tags = [])
    {
        $this->title = $title;
        $this->categoryOption = $categoryOption;
        $this->qty = $qty;
        $this->createdBy = $createdBy;
        $this->tags = $tags;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCategoryOption(): string
    {
        return $this->categoryOption;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }
}