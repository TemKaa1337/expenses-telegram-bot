<?php declare(strict_types=1);

namespace App\Services\Commands;

use App\Services\Formatters\EachMonthExpensesFormatter;
use App\Exceptions\ExpenseNotFoundException;
use App\Services\Expense;

final readonly class TotalMonthExpensesCommand extends BaseCommand
{
    /**
     * @return string
     * @throws ExpenseNotFoundException
     */
    public function execute(): string
    {
        $expensesService = new Expense($this->db, $this->user);
        $expenses = $expensesService->getEachMonthExpenses();
        return EachMonthExpensesFormatter::format($expenses);
    }
}