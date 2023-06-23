<?php declare(strict_types=1);

namespace App\Services\Commands;

use App\Exceptions\ExpenseNotFoundException;
use App\Messages\Success;
use App\Services\Expense;

final readonly class DeleteExpenseCommand extends BaseCommand
{
    /**
     * @return string
     * @throws ExpenseNotFoundException
     */
    public function execute(): string
    {
        $expenseId = (int) $this->arguments[0];
        $expenseService = new Expense($this->db, $this->user);
        $expense = $expenseService->find($expenseId);
        $expenseService->delete($expense);
        return Success::ExpenseDeleted->value;
    }
}