<?php

namespace App\Controllers;

use App\Auth;
use App\Models\Category;
use App\Repositories\MysqlCategoriesRepository;
use App\Validation\FormValidationException;
use App\Validation\TitleFormsValidator;
use App\ViewRender;
use Godruoyi\Snowflake\Snowflake;

class CategoriesController
{
    private MysqlCategoriesRepository $categoriesRepository;
    private TitleFormsValidator $formValidator;

    public function __construct()
    {
        $this->categoriesRepository = new MysqlCategoriesRepository();
        $this->formValidator = new TitleFormsValidator();
    }

    public function categoryForm(): ViewRender
    {
        if (Auth::loggedIn()) {
            return new ViewRender('Catalog/category.twig');
        }
        return ViewRender::login();
    }

    public function save(): ViewRender
    {
        try {
            $this->formValidator->validate($_POST);
            $id = new Snowflake();
            $category = new Category(
                $id->id(),
                $_POST['title']
            );
            $this->categoriesRepository->add($category);

            return new ViewRender('Catalog/category.twig');

        } catch (FormValidationException $exception) {
            $_SESSION['errors'] = $this->formValidator->getErrors();
            return new ViewRender('Catalog/category.twig', ['errors' => $_SESSION['errors']]);
        }
    }
}