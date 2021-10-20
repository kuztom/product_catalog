<?php

namespace App\Validation;

use App\Validation\FormValidationException;

class ProductsValidator
{
    private array $errors = [];

    public function validate(array $postData): void
    {
        $errorCount = 1;

        if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $postData['qty'])) {
            $this->errors['qty_' . $errorCount] = 'Dont use any special characters in input fields!';
            $errorCount++;
        }

        if (!is_numeric($postData['qty'])) {
            $this->errors['qty_' . $errorCount] = 'Use only numbers(0-9) in quantity!';
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