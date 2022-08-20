<?php declare(strict_types = 1);

namespace App\Services\Validator;

interface Validator
{
    public function validate(): array;
}