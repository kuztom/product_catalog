<?php

namespace App\Validation;

use App\Validation\FormValidationException;

class FormsValidator
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
            $this->errors['title_' . $errorCount] = 'Dont use any special characters in input fields!';
            $errorCount++;
        }

        if (is_numeric($postData['title'])) {
            $this->errors['title_' . $errorCount] = 'You cant use only numbers in title!';
            $errorCount++;
        }

        if (is_numeric($postData['title'])) {
            $this->errors['title_' . $errorCount] = 'You cant use only numbers in title!';
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