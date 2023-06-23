<?php declare(strict_types=1);

namespace App\Services\Commands;

use App\Exceptions\CategoryNotFoundException;
use App\Services\Category;
use App\Services\CategoryAlias;
use App\Services\Formatters\CategoryAliasesFormatter;

final readonly class AllCategoryAliasesCommand extends BaseCommand
{
    /**
     * @return string
     * @throws CategoryNotFoundException
     */
    public function execute(): string
    {
        $categoryService = new Category($this->db, $this->user);
        $categories = $categoryService->getAll();

        $result = [];
        $categoryAliasService = new CategoryAlias($this->db, $this->user);
        foreach ($categories as $category) {
            $aliases = $categoryAliasService->getAllByCategory($category->id);
            $result[] = CategoryAliasesFormatter::format($category->name, $aliases);
        }
        return implode(PHP_EOL, $result);
    }
}