<?php

namespace App\Controllers;

use App\Auth;
use App\Services\Categories\SaveCategoriesService;
use App\Services\Categories\SaveCategoriesRequest;
use App\Validation\FormValidationException;
use App\Validation\TitleFormsValidator;
use App\ViewRender;

class CategoriesController
{
    private TitleFormsValidator $formValidator;
    private SaveCategoriesService $saveCategoriesService;

    public function __construct()
    {
        $this->formValidator = new TitleFormsValidator();
        $this->saveCategoriesService = new SaveCategoriesService();
    }

    public function categoryForm(): ViewRender
    {
        if (Auth::loggedIn()) {
            return ViewRender::newCategory();
        }
        return ViewRender::login();
    }

    public function save(): ViewRender
    {
        $categoryTitle = $_POST['title'];
        try {
            $this->formValidator->validate($_POST);
            $this->saveCategoriesService->execute(new SaveCategoriesRequest($categoryTitle));

            return ViewRender::newCategory();

        } catch (FormValidationException $exception) {
            $_SESSION['errors'] = $this->formValidator->getErrors();
            return new ViewRender('Catalog/category.twig', ['errors' => $_SESSION['errors']]);
        }
    }
}