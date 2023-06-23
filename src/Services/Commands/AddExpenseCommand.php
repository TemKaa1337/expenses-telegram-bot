<?php declare(strict_types=1);

namespace App\Services\Commands;

use App\Exceptions\CategoryNotFoundException;
use App\Messages\Success;
use App\Services\Category;
use App\Services\Expense;

final readonly class AddExpenseCommand extends BaseCommand
{
    /**
     * @return string
     * @throws CategoryNotFoundException
     */
    public function execute(): string
    {
        $amount = round((float) $this->arguments[0], 2);
        $alias = $this->arguments[1];
        $note = $this->arguments[2] ?? null;

        $categoryService = new Category($this->db, $this->user);
        $category = $categoryService->findByAlias($alias);

        $expenseService = new Expense($this->db, $this->user);
        $expenseService->create(
            $amount,
            $category->id,
            $note
        );

        return Success::ExpenseAdded->value;
    }
}