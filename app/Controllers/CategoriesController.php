<?php

namespace App\Controllers;

use App\Models\Category;
use App\Repositories\MysqlCategoriesRepository;
use App\ViewRender;
use Godruoyi\Snowflake\Snowflake;

class CategoriesController
{
    private MysqlCategoriesRepository $categoriesRepository;

    public function __construct()
    {
        $this->categoriesRepository = new MysqlCategoriesRepository();
    }

    public function categoryForm(): ViewRender
    {
        return new ViewRender('Catalog/category.twig');
    }

    public function save(): ViewRender
    {
        $id = new Snowflake();
        $category = new Category(
            $id->id(),
            $_POST['title']
        );
        $this->categoriesRepository->add($category);

        $categories = $this->categoriesRepository->getAll();

        return new ViewRender('Catalog/add.twig', [
            'categories' => $categories
        ]);
    }
}