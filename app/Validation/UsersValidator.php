<?php

namespace App\Validation;

use App\Repositories\MysqlUsersRepository;
use App\Validation\FormValidationException;

class UsersValidator
{
    private array $errors = [];
    private MysqlUsersRepository $usersRepository;

    public function __construct()
    {
        $this->usersRepository = new MysqlUsersRepository();
    }

    public function validate(array $postData): void
    {
        $errorCount = 1;

        if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $postData['username'])) {
            $this->errors['username_' . $errorCount] = 'Dont use any special characters in input fields!';
            $errorCount++;
        }

        if (is_numeric($postData['username'])) {
            $this->errors['username_' . $errorCount] = 'You cant use only numbers for username!';
            $errorCount++;
        }

        if ($this->usersRepository->find($postData['username']) !== null) {
            $this->errors['username_' . $errorCount] = 'Username already taken!';
            $errorCount++;
        }

        if (strlen($postData['password']) < 8) {
            $this->errors['password_' . $errorCount] = 'Password too short! Must have 8 or more symbols!';
            $errorCount++;
        }

        if (is_numeric($postData['password']) || ctype_alpha($postData['password'])) {
            $this->errors['password_' . $errorCount] = 'Password too weak!';
            $errorCount++;
        }

        if (count($this->errors) > 0) {
            throw new FormValidationException();
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }


}