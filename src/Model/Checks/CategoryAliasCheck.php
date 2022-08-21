<?php declare(strict_types = 1);

namespace App\Model\Checks;

use App\Exception\CategoryAliasAlreadyExistException;
use App\Exception\NoSuchCategoryAliasException;

trait CategoryAliasCheck
{
    private function checkIfCategoryAliasExists(): void
    {
        if (!isset($this->aliasId)) {
            throw new NoSuchCategoryAliasException('Такого алиаса категории не существует.');
        }
    }

    private function checkIfCategoryAliasDoesntExist(): void
    {
        if (isset($this->aliasId)) {
            throw new CategoryAliasAlreadyExistException('Такой алиас категории уже существует');
        }
    }
}