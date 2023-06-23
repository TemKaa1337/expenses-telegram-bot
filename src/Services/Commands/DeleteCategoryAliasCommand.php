<?php declare(strict_types=1);

namespace App\Services\Commands;

use App\Exceptions\CategoryAliasNotFoundException;
use App\Exceptions\CategoryNotFoundException;
use App\Messages\Success;
use App\Services\Category;
use App\Services\CategoryAlias;

final readonly class DeleteCategoryAliasCommand extends BaseCommand
{
    /**
     * @return string
     * @throws CategoryAliasNotFoundException
     * @throws CategoryNotFoundException
     */
    public function execute(): string
    {
        $categoryAlias = $this->arguments[0];
        $categoryService = new Category($this->db, $this->user);
        $category = $categoryService->findByAlias($categoryAlias);

        $categoryAliasService = new CategoryAlias($this->db, $this->user);
        $alias = $categoryAliasService->findByName($category->id, $categoryAlias);
        $categoryAliasService->delete($alias);
        return Success::CategoryAliasDeleted->value;
    }
}