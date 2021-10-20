<?php

namespace App\Middlewares;

use App\Auth;
use App\ViewRender;

class AuthorizedMiddleware implements \App\Middlewares\Middleware
{
    public function handle()
    {
        if (!Auth::loggedIn()) {
             ViewRender::login();
        }
    }
}