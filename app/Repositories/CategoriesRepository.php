<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Collections\CategoriesCollection;

interface CategoriesRepository
{
    public function add(Category $category): void;
    public function getAll(): CategoriesCollection;
}