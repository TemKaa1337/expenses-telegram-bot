<?php declare(strict_types = 1);

namespace App\Model\Checks;

use App\Exception\CategoryAliasAlreadyExistException;
use App\Exception\NoSuchCategoryAliasException;
use App\Messages\ErrorMessage;

trait CategoryAliasCheck
{
    private function checkIfCategoryAliasExists(): void
    {
        if (!isset($this->aliasId)) {
            throw new NoSuchCategoryAliasException(ErrorMessage::UnknownCategoryAlias->value);
        }
    }

    private function checkIfCategoryAliasDoesntExist(): void
    {
        if (isset($this->aliasId)) {
            throw new CategoryAliasAlreadyExistException(ErrorMessage::CategoryAliasAlreadyExists->value);
        }
    }
}