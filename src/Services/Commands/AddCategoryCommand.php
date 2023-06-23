<?php declare(strict_types=1);

namespace App\Services\Commands;

use App\Exceptions\CategoryAlreadyExistException;
use App\Exceptions\CategoryNotFoundException;
use App\Messages\Error;
use App\Messages\Success;
use App\Services\Category;
use App\Services\CategoryAlias;

final readonly class AddCategoryCommand extends BaseCommand
{
    /**
     * @return string
     * @throws CategoryAlreadyExistException
     */
    public function execute(): string
    {
        $name = $this->arguments[0];
        $categoryService = new Category($this->db, $this->user);
        try {
            $categoryService->findByName($name);
            throw new CategoryAlreadyExistException(Error::CategoryAlreadyExist->value);
        } catch (CategoryNotFoundException $e) {}

        $category = $categoryService->create($name);
        $categoryAliasService = new CategoryAlias($this->db, $this->user);
        $categoryAliasService->create($category->id, $name);
        return Success::CategoryAdded->value;
    }
}