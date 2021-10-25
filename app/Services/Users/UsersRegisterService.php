<?php

namespace App\Services\Users;

use App\Models\User;
use App\Repositories\MysqlUsersRepository;
use Godruoyi\Snowflake\Snowflake;

class UsersRegisterService
{
    private MysqlUsersRepository $usersRepository;

    public function __construct()
    {
        $this->usersRepository = new MysqlUsersRepository();
    }

    public function execute(UsersRegisterRequest $request): void
    {
        $id = new Snowflake();
        $user = new User(
            $id->id(),
            $request->getUsername(),
            $request->getEmail(),
            $request->getPassword()
        );

        $this->usersRepository->add($user);
    }
}