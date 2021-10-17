<?php

namespace App\Repositories;

use App\Models\Product;

interface ProductsRepository
{
    public function add(Product $product): void;
}