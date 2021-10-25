<?php

namespace App\Controllers;

use App\Auth;
use App\Repositories\MysqlCategoriesRepository;
use App\Repositories\MysqlProductsRepository;
use App\Repositories\MysqlTagsRepository;
use App\Services\Products\DeleteProductsRequest;
use App\Services\Products\DeleteProductsService;
use App\Services\Products\EditProductsRequest;
use App\Services\Products\EditProductsService;
use App\Services\Products\SaveProductsRequest;
use App\Services\Products\SaveProductsService;
use App\Validation\FormValidationException;
use App\Validation\ProductsValidator;
use App\ViewRender;

class ProductsController
{
    private MysqlProductsRepository $productsRepository;
    private MysqlCategoriesRepository $categoriesRepository;
    private MysqlTagsRepository $tagsRepository;
    private ProductsValidator $productsValidator;

    public function __construct()
    {
        $this->productsRepository = new MysqlProductsRepository();
        $this->categoriesRepository = new MysqlCategoriesRepository();
        $this->tagsRepository = new MysqlTagsRepository();
        $this->productsValidator = new ProductsValidator();
    }

    public function catalogRedirect(): ViewRender
    {
        $products = $this->productsRepository->getAll();
        $categories = $this->categoriesRepository->getAll();
        $tags = $this->tagsRepository->getAvailableTags();

        return ViewRender::catalog($products, $categories, $tags);
    }

    public function catalog(): ViewRender
    {
        if (Auth::loggedIn()) {
            return $this->catalogRedirect();
        }
        return ViewRender::login();
    }

    public function filterCatalog(): ViewRender
    {
        $products = $this->productsRepository->getCategory($_POST['categoryOption']);
        $categories = $this->categoriesRepository->getAll();
        $tags = $this->tagsRepository->getAvailableTags();

        return ViewRender::catalog($products, $categories, $tags);
    }

    public function addForm(): ViewRender
    {
        if (Auth::loggedIn()) {
            $categories = $this->categoriesRepository->getAll();
            $tags = $this->tagsRepository->getAll();
            $_SESSION['errors'] = $this->productsValidator->getErrors();
            $errors = $_SESSION['errors'];
            return ViewRender::newProduct($categories, $tags, $errors);
        }
        return ViewRender::login();
    }

    public function save(): ViewRender
    {
        $title = $_POST['title'];
        $categoryOption = $_POST['categoryOption'];
        $qty = $_POST['qty'];
        $createdBy = $_SESSION['username'];
        $tags = $_POST['product_tags'];

        try {

            $this->productsValidator->validate($_POST);

            (new SaveProductsService())->execute(
                new SaveProductsRequest($title, $categoryOption, $qty, $createdBy, $tags)
            );

            return $this->addForm();

        } catch (FormValidationException $exception) {
            $_SESSION['errors'] = $this->productsValidator->getErrors();
            $errors = $_SESSION['errors'];
            $categories = $this->categoriesRepository->getAll();
            $tags = $this->tagsRepository->getAll();
            return ViewRender::newProduct($categories, $tags, $errors);
        }
    }

    public function productForm(array $vars): ViewRender
    {
        if (Auth::loggedIn()) {
            $id = $vars['id'];
            $product = $this->productsRepository->getOne($id);
            $categories = $this->categoriesRepository->getAll();
            return ViewRender::product($product, $categories);
        }
        return ViewRender::login();
    }

    public function editProduct(array $vars): ViewRender
    {
        if (Auth::loggedIn()) {
            $id = $vars['id'];

            if ($_POST['action'] === 'Save') {
                (new EditProductsService())->execute(
                    new EditProductsRequest($id)
                );

                $product = $this->productsRepository->getOne($id);
                $categories = $this->categoriesRepository->getAll();

                return ViewRender::product($product, $categories);
            }

            if ($_POST['action'] === 'Delete') {
                (new DeleteProductsService())->execute(
                    new DeleteProductsRequest($id)
                );

                return $this->catalog();
            }
        }
        return ViewRender::login();
    }
}