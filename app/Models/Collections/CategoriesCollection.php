<?php

namespace App\Models\Collections;

use App\Models\Category;

class CategoriesCollection
{
    private array $categories;

    public function __construct(array $categories = [])
    {
        foreach ($categories as $category) {
            $this->add($category);
        }
    }

    public function add(Category $category)
    {
        $this->categories[$category->getId()] = $category;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }
}
