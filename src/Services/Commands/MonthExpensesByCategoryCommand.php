<?php declare(strict_types=1);

namespace App\Services\Commands;

use App\Exceptions\ExpenseNotFoundException;
use App\Exceptions\InvalidInputException;
use App\Services\Expense;
use App\Services\Formatters\ExpensesByCategoryFormatter;
use App\Services\Validators\DatePartValidator;

final readonly class MonthExpensesByCategoryCommand extends BaseCommand
{
    /**
     * @return string
     * @throws ExpenseNotFoundException
     * @throws InvalidInputException
     */
    public function execute(): string
    {
        $date = $this->arguments[0];
        $dateValidator = new DatePartValidator($date, allowEmptyDate: true);
        $dateInfo = $dateValidator->validate();

        $expenseService = new Expense($this->db, $this->user);
        $expenses = $expenseService->getAllBetweenDates($dateInfo['startDate'], $dateInfo['endDate']);
        return ExpensesByCategoryFormatter::format($expenses);
    }
}