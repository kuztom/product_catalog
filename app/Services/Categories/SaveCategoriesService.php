<?php

namespace App\Services\Categories;

use App\Models\Category;
use App\Repositories\MysqlCategoriesRepository;
use Godruoyi\Snowflake\Snowflake;

class SaveCategoriesService
{
    private MysqlCategoriesRepository $categoriesRepository;

    public function __construct()
    {
        $this->categoriesRepository = new MysqlCategoriesRepository();
    }

    public function execute(SaveCategoriesRequest $request): void
    {
        $id = new Snowflake();
            $category = new Category(
                $id->id(),
                $request->getCategoryTitle()
            );
            $this->categoriesRepository->add($category);
    }

}