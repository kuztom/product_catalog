<?php

namespace App\Middlewares;

use App\Auth;
use App\ViewRender;

class AuthorizedMiddleware implements Middleware
{
    public function handle()
    {
        if (!Auth::loggedIn()) {
             ViewRender::login();
        }
    }
}