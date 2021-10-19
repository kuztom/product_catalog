<?php

namespace App\Controllers;

use App\Models\Product;
use App\Repositories\MysqlCategoriesRepository;
use App\Repositories\MysqlProductsRepository;
use App\Repositories\MysqlTagsRepository;
use App\ViewRender;
use Godruoyi\Snowflake\Snowflake;

class ProductsController
{
    private MysqlProductsRepository $productsRepository;
    private MysqlCategoriesRepository $categoriesRepository;
    private MysqlTagsRepository $tagsRepository;

    public function __construct()
    {
        $this->productsRepository = new MysqlProductsRepository();
        $this->categoriesRepository = new MysqlCategoriesRepository();
        $this->tagsRepository = new MysqlTagsRepository();
    }

    public function catalog(): ViewRender
    {
        $products = $this->productsRepository->getAll();
        $categories = $this->categoriesRepository->getAll();
        $tags = $this->tagsRepository->getAll();

        return new ViewRender('Catalog/catalog.twig', [
            'products' => $products,
            'categories' => $categories,
            'tags' => $tags
        ]);
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

    public function addForm(): ViewRender
    {
        $categories = $this->categoriesRepository->getAll();
        $tags = $this->tagsRepository->getAll();
        return new ViewRender('Catalog/add.twig', [
            'categories' => $categories,
            'tags' => $tags
        ]);
    }

    public function save()
    {
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
    }

    public function productForm(array $vars): ViewRender
    {
        $id = $vars['id'];
        $product = $this->productsRepository->getOne($id);
        $categories = $this->categoriesRepository->getAll();
        return new ViewRender('Catalog/product.twig', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    public function editProduct(array $vars)
    {
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


}