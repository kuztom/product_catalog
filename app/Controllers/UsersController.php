<?php

namespace App\Controllers;

use App\Auth;
use App\Models\User;
use App\Repositories\MysqlCategoriesRepository;
use App\Repositories\MysqlProductsRepository;
use App\Repositories\MysqlTagsRepository;
use App\Repositories\MysqlUsersRepository;
use App\Validation\FormValidationException;
use App\Validation\UsersValidator;
use App\ViewRender;
use Godruoyi\Snowflake\Snowflake;

class UsersController
{
    private MysqlUsersRepository $usersRepository;
    private MysqlProductsRepository $productsRepository;
    private MysqlCategoriesRepository $categoriesRepository;
    private MysqlTagsRepository $tagsRepository;
    private UsersValidator $usersValidator;

    public function __construct()
    {
        $this->usersRepository = new MysqlUsersRepository();
        $this->productsRepository = new MysqlProductsRepository();
        $this->categoriesRepository = new MysqlCategoriesRepository();
        $this->tagsRepository = new MysqlTagsRepository();
        $this->usersValidator = new UsersValidator();
    }

    public function index(): ViewRender
    {
        return new ViewRender('index.twig');
    }

    public function catalogRedirect(): ViewRender
    {
        $products = $this->productsRepository->getAll();
        $categories = $this->categoriesRepository->getAll();
        $tags = $this->tagsRepository->getAll();

        return ViewRender::catalog($products, $categories, $tags);
    }

    public function loginForm(): ViewRender
    {
        if (Auth::loggedIn()) {
            return $this->catalogRedirect();
        }
        return ViewRender::login();
    }

    public function login()
    {

        $user = $this->usersRepository->find($_POST['username']);

        if ($user !== null && password_verify($_POST['password'], $user->getPassword())) {
            $_SESSION['username'] = $user->getUsername();
            $products = $this->productsRepository->getAll();
            $categories = $this->categoriesRepository->getAll();
            $tags = $this->tagsRepository->getAll();
            return new ViewRender('catalog/catalog.twig', [

                'products' => $products,
                'categories' => $categories,
                'tags' => $tags
            ]);

        } else {
            return ViewRender::login();
        }
    }

    public function logout()
    {
        session_unset();
        return $this->loginForm();
    }

    public function registerForm(): ViewRender
    {
        if (Auth::loggedIn()) {
            return $this->catalogRedirect();
        }
        return new ViewRender('Users/register.twig');
    }

    public function register()
    {
        try {
            $this->usersValidator->validate($_POST);
            $id = new Snowflake();
            $user = new User(
                $id->id(),
                $_POST['username'],
                $_POST['email'],
                password_hash($_POST['password'], PASSWORD_DEFAULT));

            $this->usersRepository->add($user);

            return ViewRender::login();
        } catch (FormValidationException $exception) {
            $_SESSION['errors'] = $this->usersValidator->getErrors();
            return new ViewRender('Users/register.twig', [
                'errors' => $_SESSION['errors']
            ]);
        }
    }
}