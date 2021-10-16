<?php

namespace App\Repositories;

use App\Models\User;

interface UsersRepository
{
    public function add(User $user): void;
    public function find(string $username): ?User;

}