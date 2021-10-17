<?php

namespace App\Controllers;

use App\Models\User;
use App\Repositories\MysqlCategoriesRepository;
use App\Repositories\MysqlProductsRepository;
use App\Repositories\MysqlUsersRepository;
use App\ViewRender;
use Godruoyi\Snowflake\Snowflake;

class UsersController
{
    private MysqlUsersRepository $usersRepository;
    private MysqlProductsRepository $productsRepository;

    public function __construct()
    {
        $this->usersRepository = new MysqlUsersRepository();
        $this->productsRepository = new MysqlProductsRepository();
    }

    public function index(): ViewRender
    {
        return new ViewRender('index.twig');
    }

    public function loginForm(): ViewRender
    {

        return new ViewRender('Users/login.twig');
    }

    public function login()
    {
        $user = $this->usersRepository->find($_POST['username']);
        $products = $this->productsRepository->getAll();
        if ($user !== null && password_verify($_POST['password'], $user->getPassword())) {
            return new ViewRender('Catalog/catalog.twig', [
                'user' => $user,
                'products' => $products
            ]);
        } else {
            return new ViewRender('Users/login.twig');
        }
    }

    public function registerForm(): ViewRender
    {
        return new ViewRender('Users/register.twig');
    }

    public function register()
    {
        $id = new Snowflake();
        $user = new User(
            $id->id(),
            $_POST['username'],
            $_POST['email'],
            password_hash($_POST['password'], PASSWORD_DEFAULT));

        $this->usersRepository->add($user);

        return new ViewRender('Users/login.twig');
    }
}