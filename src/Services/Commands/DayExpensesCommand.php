<?php declare(strict_types=1);

namespace App\Services\Commands;

use App\Exceptions\ExpenseNotFoundException;
use App\Exceptions\InvalidInputException;
use App\Services\Expense;
use App\Services\Formatters\IntervalExpensesFormatter;
use App\Services\Validators\DateValidator;

final readonly class DayExpensesCommand extends BaseCommand
{
    /**
     * @return string
     * @throws InvalidInputException
     * @throws ExpenseNotFoundException
     */
    public function execute(): string
    {
        $date = $this->arguments[0] ?? '';
        $dateValidator = new DateValidator($date, allowEmptyDate: true);
        $dateInfo = $dateValidator->validate();

        $expensesService = new Expense($this->db, $this->user);
        $expenses = $expensesService->getAllBetweenDates($dateInfo['startDate'], $dateInfo['endDate']);
        return IntervalExpensesFormatter::format($expenses, $dateInfo['startDate'], skipDayAvg: true);
    }
}