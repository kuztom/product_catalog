<?php

namespace App\Validation;

use App\Validation\FormValidationException;

class CategoriesValidator
{
    private array $errors = [];

    public function validate(array $postData): void
    {
        $errorCount = 1;
        if (empty($postData['title'])) {
            $this->errors['title_' . $errorCount] = 'Title is required!';
            $errorCount++;
        }

        if (strlen($postData['title']) < 2) {
            $this->errors['title_' . $errorCount] = 'Title length must be two or more characters!';
            $errorCount++;
        }

        if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $postData['title'])) {
            $this->errors['title_' . $errorCount] = 'For category name use only letters or numbers!';
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