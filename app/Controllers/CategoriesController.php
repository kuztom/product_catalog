<?php

namespace App\Controllers;

use App\Models\Category;
use App\Repositories\MysqlCategoriesRepository;
use App\Validation\CategoriesValidator;
use App\Validation\FormValidationException;
use App\ViewRender;
use Godruoyi\Snowflake\Snowflake;

class CategoriesController
{
    private MysqlCategoriesRepository $categoriesRepository;
    private CategoriesValidator $categoriesValidator;

    public function __construct()
    {
        $this->categoriesRepository = new MysqlCategoriesRepository();
        $this->categoriesValidator = new CategoriesValidator();
    }

    public function categoryForm()
    {
        if (isset($_SESSION['username'])) {
            return new ViewRender('Catalog/category.twig');
        }
        header('Location: /login');
    }

    public function save(): ViewRender
    {
        try {
            $this->categoriesValidator->validate($_POST);
            $id = new Snowflake();
            $category = new Category(
                $id->id(),
                $_POST['title']
            );
            $this->categoriesRepository->add($category);

            return new ViewRender('Catalog/category.twig');

        } catch (FormValidationException $exception) {
            $_SESSION['errors'] = $this->categoriesValidator->getErrors();
            return new ViewRender('Catalog/category.twig', ['errors' => $_SESSION['errors']]);
        }
    }
}