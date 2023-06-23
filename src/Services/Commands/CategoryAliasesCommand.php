<?php declare(strict_types=1);

namespace App\Services\Commands;

use App\Exceptions\CategoryNotFoundException;
use App\Services\Category;
use App\Services\CategoryAlias;
use App\Services\Formatters\CategoryAliasesFormatter;

final readonly class CategoryAliasesCommand extends BaseCommand
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

        $categoryAliasService = new CategoryAlias($this->db, $this->user);
        $aliases = $categoryAliasService->getAllByCategory($category->id);
        return CategoryAliasesFormatter::format($category->name, $aliases);
    }
}