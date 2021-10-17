<?php

namespace App\Controllers;

use App\Models\Product;
use App\Repositories\MysqlCategoriesRepository;
use App\Repositories\MysqlProductsRepository;
use App\ViewRender;
use Godruoyi\Snowflake\Snowflake;

class ProductsController
{
    private MysqlProductsRepository $productsRepository;
    private MysqlCategoriesRepository $categoriesRepository;

    public function __construct()
    {
        $this->productsRepository = new MysqlProductsRepository();
        $this->categoriesRepository = new MysqlCategoriesRepository();
    }

    public function catalog(): ViewRender
    {
        $products = $this->productsRepository->getAll();

        return new ViewRender('Catalog/catalog.twig', [
            'products' => $products
        ]);
    }

    public function addForm(): ViewRender
    {
        $categories = $this->categoriesRepository->getAll();
        return new ViewRender('Catalog/add.twig', [
            'categories' => $categories
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
            "username",
            date('Y-m-d H:i:s')
        );

        $this->productsRepository->add($product);

        return new ViewRender('Catalog/add.twig');
    }

    public function productForm(array $vars): ViewRender
    {
        $id = $vars['id'];
        $product = $this->productsRepository->getOne($id);
        return new ViewRender('Catalog/product.twig', [
            'product' => $product
        ]);
    }

}