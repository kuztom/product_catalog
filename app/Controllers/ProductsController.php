<?php

namespace App\Controllers;

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

    public function catalog()
    {
        if (isset($_SESSION['username'])) {
            $products = $this->productsRepository->getAll();
            $categories = $this->categoriesRepository->getAll();
            $tags = $this->tagsRepository->getAll();

            return new ViewRender('Catalog/catalog.twig', [
                'products' => $products,
                'categories' => $categories,
                'tags' => $tags
            ]);
        }
        header('Location: /login');
    }

    public function filterCatalog(): ViewRender
    {
        $products = $this->productsRepository->getCategory($_POST['categoryOption']);
        $categories = $this->categoriesRepository->getAll();
        $tags = $this->tagsRepository->getAll();

        return new ViewRender('Catalog/catalog.twig', [
            'products' => $products,
            'categories' => $categories,
            'tags' => $tags
        ]);
    }

    public function addForm()
    {
        if (isset($_SESSION['username'])) {
            $categories = $this->categoriesRepository->getAll();
            $tags = $this->tagsRepository->getAll();
            return new ViewRender('Catalog/add.twig', [
                'categories' => $categories,
                'tags' => $tags
            ]);
        }
        header('Location: /login');
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
                date('Y-m-d H:i:s')
            );

            $this->productsRepository->add($product);

            return $this->addForm();
        } catch (FormValidationException $exception) {
            $_SESSION['errors'] = $this->productsValidator->getErrors();
            $categories = $this->categoriesRepository->getAll();
            $tags = $this->tagsRepository->getAll();
            return new ViewRender('Catalog/add.twig', [
                'errors' => $_SESSION['errors'],
                'categories' => $categories,
                'tags' => $tags]);
        }
    }

    public function productForm(array $vars)
    {
        if (isset($_SESSION['username'])) {
            $id = $vars['id'];
            $product = $this->productsRepository->getOne($id);
            $categories = $this->categoriesRepository->getAll();
            return new ViewRender('Catalog/product.twig', [
                'product' => $product,
                'categories' => $categories
            ]);
        }
        header('Location: /login');
    }

    public function editProduct(array $vars)
    {
        if (isset($_SESSION['username'])) {
            $id = $vars['id'];

            if ($_POST['action'] === 'Save') {
                $this->productsRepository->saveEdit($id);

                $product = $this->productsRepository->getOne($id);
                $categories = $this->categoriesRepository->getAll();

                return new ViewRender('Catalog/product.twig', [
                    'product' => $product,
                    'categories' => $categories
                ]);
            }

            if ($_POST['action'] === 'Delete') {
                $this->productsRepository->delete($id);
                return $this->catalog();
            }
        }
        header('Location: /login');
    }


}