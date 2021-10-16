<?php

namespace App\Controllers;

use App\ViewRender;

class ProductsController
{

    public function catalog()
    {
        return new ViewRender('Catalog/catalog.twig');
    }

}