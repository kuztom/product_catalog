<?php

namespace App\Controllers;

use App\Auth;
use App\Repositories\MysqlCategoriesRepository;
use App\Repositories\MysqlProductsRepository;
use App\Repositories\MysqlTagsRepository;
use App\Services\Users\UsersLoginRequest;
use App\Services\Users\UsersLoginService;
use App\Services\Users\UsersRegisterRequest;
use App\Services\Users\UsersRegisterService;
use App\Validation\FormValidationException;
use App\Validation\UsersValidator;
use App\ViewRender;

class UsersController
{
    private MysqlProductsRepository $productsRepository;
    private MysqlCategoriesRepository $categoriesRepository;
    private MysqlTagsRepository $tagsRepository;
    private UsersValidator $usersValidator;

    public function __construct()
    {
        $this->productsRepository = new MysqlProductsRepository();
        $this->categoriesRepository = new MysqlCategoriesRepository();
        $this->tagsRepository = new MysqlTagsRepository();
        $this->usersValidator = new UsersValidator();
    }

    public function index(): ViewRender
    {
        return ViewRender::frontPage();
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

    public function login(): ViewRender
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $response = new UsersLoginService();

        if ($response->execute(new UsersLoginRequest($username, $password)) !== null) {
            $_SESSION['username'] = $username;
            $products = $this->productsRepository->getAll();
            $categories = $this->categoriesRepository->getAll();
            $tags = $this->tagsRepository->getAll();
            return ViewRender::catalog($products, $categories, $tags);
        } else {
            return ViewRender::login();
        }
    }

    public function logout(): ViewRender
    {
        session_unset();
        return $this->loginForm();
    }

    public function registerForm(): ViewRender
    {
        if (Auth::loggedIn()) {
            return $this->catalogRedirect();
        }
        return ViewRender::register();
    }

    public function register(): ViewRender
    {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        try {
            $this->usersValidator->validate($_POST);
            (new UsersRegisterService())->execute(
                new UsersRegisterRequest($username, $email, $password));

            return ViewRender::login();
        } catch (FormValidationException $exception) {
            $_SESSION['errors'] = $this->usersValidator->getErrors();
            return new ViewRender('Users/register.twig', [
                'errors' => $_SESSION['errors']
            ]);
        }
    }
}