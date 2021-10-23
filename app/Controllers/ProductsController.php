<?php

namespace App\Controllers;

use App\Auth;
use App\Models\Product;
use App\Repositories\MysqlCategoriesRepository;
use App\Repositories\MysqlProductsRepository;
use App\Repositories\MysqlTagsRepository;
use App\Validation\FormValidationException;
use App\Validation\ProductsValidator;
use App\ViewRender;
use Godruoyi\Snowflake\Snowflake;

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

    public function catalog()
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

    public function addForm()
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

    public function save()
    {
        try {

            $this->productsValidator->validate($_POST);

            $id = new Snowflake();
            $product = new Product(
                $id->id(),
                $_POST['title'],
                $_POST['categoryOption'],
                $_POST['qty'],
                date('Y-m-d H:i:s'),
                $_SESSION['username'],
                date('Y-m-d H:i:s'),
                $_POST['product_tags']
            );

            $this->productsRepository->add($product);
            $this->productsRepository->saveProductTags($product);

            return $this->addForm();

        } catch (FormValidationException $exception) {
            $_SESSION['errors'] = $this->productsValidator->getErrors();
            $errors = $_SESSION['errors'];
            $categories = $this->categoriesRepository->getAll();
            $tags = $this->tagsRepository->getAll();
            return ViewRender::newProduct($categories, $tags, $errors);
        }
    }

    public function productForm(array $vars)
    {
        if (Auth::loggedIn()) {
            $id = $vars['id'];
            $product = $this->productsRepository->getOne($id);
            $categories = $this->categoriesRepository->getAll();
            return ViewRender::product($product, $categories);
        }
        return ViewRender::login();
    }

    public function editProduct(array $vars)
    {
        if (Auth::loggedIn()) {
            $id = $vars['id'];

            if ($_POST['action'] === 'Save') {
                $this->productsRepository->saveEdit($id);

                $product = $this->productsRepository->getOne($id);
                $categories = $this->categoriesRepository->getAll();

                return ViewRender::product($product, $categories);
            }

            if ($_POST['action'] === 'Delete') {
                $this->productsRepository->delete($id);
                return $this->catalog();
            }
        }
        return ViewRender::login();
    }

}