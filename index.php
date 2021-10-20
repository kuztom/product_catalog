<?php
session_start();
date_default_timezone_set('Europe/Riga');

use App\Middlewares\AuthorizedMiddleware;
use App\ViewRender;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once 'vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {

    $r->addRoute('GET', '/', 'UsersController@index');

    $r->addRoute('GET', '/register', 'UsersController@registerForm');
    $r->addRoute('POST', '/register', 'UsersController@register');
    $r->addRoute('POST', '/catalog', 'UsersController@login');
    $r->addRoute('GET', '/login', 'UsersController@loginForm');
    $r->addRoute('GET', '/logout', 'UsersController@logout');

    $r->addRoute('GET', '/catalog', 'ProductsController@catalog');
    $r->addRoute('POST', '/catalog/filter', 'ProductsController@filterCatalog');
    $r->addRoute('GET', '/catalog/product/{id}', 'ProductsController@productForm');
    $r->addRoute('POST', '/catalog/product/{id}', 'ProductsController@editProduct');
    $r->addRoute('GET', '/catalog/add', 'ProductsController@addForm');
    $r->addRoute('POST', '/catalog/add', 'ProductsController@save');

    $r->addRoute('GET', '/catalog/category', 'CategoriesController@categoryForm');
    $r->addRoute('POST', '/catalog/category', 'CategoriesController@save');

    $r->addRoute('GET', '/catalog/tag', 'TagsController@tagsForm');
    $r->addRoute('POST', '/catalog/tag', 'TagsController@save');

});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

$loader = new FilesystemLoader('app/Views');
$templateEngine = new Environment($loader, []);
$templateEngine->addGlobal('session', $_SESSION);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        var_dump("404");
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        var_dump("405");
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // ... call $handler with $vars

        $middlewares = [
            'ProductsController@catalog' => [AuthorizedMiddleware::class],
            'UsersController@login' => [AuthorizedMiddleware::class],
        ];

        if (array_key_exists($handler, $middlewares)) {
            foreach ($middlewares[$handler] as $middleware) {
                (new $middleware)->handle();
            }
        }

        [$controller, $method] = explode('@', $handler);

        $controller = 'App\Controllers\\' . $controller;
        $controller = new $controller();

        $response = $controller->$method($vars);

        if ($response instanceof ViewRender) {
            echo $templateEngine->render($response->getTemplate(), $response->getVars());
        }
        break;
}

unset($_SESSION['errors']);