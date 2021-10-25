<?php

namespace App\Services\Users;

use App\Repositories\MysqlUsersRepository;
use App\ViewRender;

class UsersLoginService
{
    private MysqlUsersRepository $usersRepository;

    public function __construct()
    {
        $this->usersRepository = new MysqlUsersRepository();
    }

    public function execute(UsersLoginRequest $request)
    {
        $user = $this->usersRepository->find($request->getUsername());

        if ($user !== null && password_verify($request->getPassword(), $user->getPassword())) {
            return $user->getUsername();
        }
    }
}