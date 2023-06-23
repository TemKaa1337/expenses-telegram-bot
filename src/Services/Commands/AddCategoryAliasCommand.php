<?php declare(strict_types=1);

namespace App\Services\Commands;

use App\Exceptions\CategoryAliasAlreadyExistsException;
use App\Exceptions\CategoryAliasNotFoundException;
use App\Exceptions\CategoryNotFoundException;
use App\Messages\Error;
use App\Messages\Success;
use App\Services\Category;
use App\Services\CategoryAlias;

final readonly class AddCategoryAliasCommand extends BaseCommand
{
    /**
     * @return string
     * @throws CategoryNotFoundException
     * @throws CategoryAliasAlreadyExistsException
     */
    public function execute(): string
    {
        $categoryName = $this->arguments[0];
        $categoryAlias = $this->arguments[1];

        $categoryService = new Category($this->db, $this->user);
        $category = $categoryService->findByName($categoryName);

        $categoryAliasService = new CategoryAlias($this->db, $this->user);
        try {
            $categoryAliasService->findByName($category->id, $categoryAlias);
            throw new CategoryAliasAlreadyExistsException(Error::CategoryAliasAlreadyExists->value);
        } catch (CategoryAliasNotFoundException $e) {}

        $categoryAliasService->create($category->id, $categoryAlias);
        return Success::CategoryAliasAdded->value;
    }
}