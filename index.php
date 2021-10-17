<?php
date_default_timezone_set('Europe/Riga');

use App\ViewRender;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once 'vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {

    $r->addRoute('GET', '/', 'MainController@index');
    $r->addRoute('GET', '/register', 'UsersController@registerForm');
    $r->addRoute('POST', '/register', 'UsersController@register');
    $r->addRoute('POST', '/catalog', 'UsersController@login');
    $r->addRoute('GET', '/login', 'UsersController@loginForm');

    $r->addRoute('GET', '/catalog', 'ProductsController@catalog');
    $r->addRoute('GET', '/catalog/product/{id}', 'ProductsController@productForm');
    $r->addRoute('GET', '/catalog/add', 'ProductsController@addForm');
    $r->addRoute('POST', '/catalog/add', 'ProductsController@save');
    $r->addRoute('GET', '/catalog/category', 'CategoriesController@categoryForm');
    $r->addRoute('POST', '/catalog/category', 'CategoriesController@save');

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

        [$controller, $method] = explode('@', $handler);

        $controller = 'App\Controllers\\' . $controller;
        $controller = new $controller();

        $response = $controller->$method($vars);

        if ($response instanceof ViewRender)
        {
            echo $templateEngine->render($response->getTemplate(), $response->getVars());
        }

        break;
}
