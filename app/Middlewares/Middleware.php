<?php

namespace App\Middlewares;

use App\ViewRender;

interface Middleware
{
    public function handle();
}