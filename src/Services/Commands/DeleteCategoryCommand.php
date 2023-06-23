<?php declare(strict_types=1);

namespace App\Services\Commands;

use App\Exceptions\CategoryNotFoundException;
use App\Messages\Success;
use App\Services\Category;
use App\Services\CategoryAlias;

final readonly class DeleteCategoryCommand extends BaseCommand
{
    /**
     * @return string
     * @throws CategoryNotFoundException
     */
    public function execute(): string
    {
        $categoryName = $this->arguments[0];
        $categoryService = new Category($this->db, $this->user);
        $category = $categoryService->findByName($categoryName);
        $categoryService->delete($category);

        $categoryAliasService = new CategoryAlias($this->db, $this->user);
        $categoryAliasService->deleteAll($category->id);
        return Success::CategoryDeleted->value;
    }
}