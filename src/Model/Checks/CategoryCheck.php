<?php declare(strict_types = 1);

namespace App\Model\Checks;

use App\Exception\CategoryAlreadyExistException;
use App\Exception\NoSuchCategoryException;
use App\Messages\ErrorMessage;
use App\Model\CategoryAlias;

trait CategoryCheck
{
    public function checkIfCategoryExists(): void
    {
        if (!isset($this->categoryId)) {
            throw new NoSuchCategoryException(ErrorMessage::UnknownCategory->value);
        }
    }

    private function checkIfCategoryDoesntExist(): void
    {
        if (isset($this->categoryId)) {
            throw new CategoryAlreadyExistException(ErrorMessage::CategoryAlreadyExist->value);
        }
    }
}