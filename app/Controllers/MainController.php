<?php

namespace App\Controllers;

use App\ViewRender;

class MainController
{
    public function index(): ViewRender
    {
        return new ViewRender('index.twig');
    }
}