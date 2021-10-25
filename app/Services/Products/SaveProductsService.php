<?php

namespace App\Services\Products;

use App\Models\Product;
use App\Repositories\MysqlProductsRepository;
use Godruoyi\Snowflake\Snowflake;

class SaveProductsService
{
    private MysqlProductsRepository $productsRepository;

    public function __construct()
    {
        $this->productsRepository = new MysqlProductsRepository();
    }

    public function execute(SaveProductsRequest $request): Void
    {
        $id = new Snowflake();
        $product = new Product(
            $id->id(),
            $request->getTitle(),
            $request->getCategoryOption(),
            $request->getQty(),
            date('Y-m-d H:i:s'),
            $request->getCreatedBy(),
            date('Y-m-d H:i:s'),
            $request->getTags()
        );

        $this->productsRepository->add($product);
        $this->productsRepository->saveProductTags($product);

    }

}