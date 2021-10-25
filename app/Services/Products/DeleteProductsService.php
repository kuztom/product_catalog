<?php

namespace App\Services\Products;

use App\Repositories\MysqlProductsRepository;

class DeleteProductsService
{
    private MysqlProductsRepository $productsRepository;

    public function __construct()
    {
        $this->productsRepository = new MysqlProductsRepository();
    }

    public function execute(DeleteProductsRequest $request): void
    {
        $this->productsRepository->delete($request->getProductId());
    }
}