<?php

namespace App\Services\Categories;

class SaveCategoriesRequest
{
    private string $categoryTitle;

    public function __construct(string $categoryTitle)
    {
        $this->categoryTitle = $categoryTitle;
    }

    public function getCategoryTitle(): string
    {
        return $this->categoryTitle;
    }
}