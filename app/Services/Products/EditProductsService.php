<?php

namespace App\Services\Products;

use App\Repositories\MysqlProductsRepository;

class EditProductsService
{
    private MysqlProductsRepository $productsRepository;

    public function __construct()
    {
        $this->productsRepository = new MysqlProductsRepository();
    }

    public function execute(EditProductsRequest $request): void
    {
        $this->productsRepository->saveEdit($request->getProductId());
    }
}