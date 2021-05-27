<?php
declare(strict_types = 1);

namespace App\Command;

class CommandPool
{
    const START = '/start';
    const MONTH_EXPENSES = '/month_expenses';
    const DAY_EXPENSES = '/day_expenses';
    const PREVIOUS_MONTH_EXPENSES = '/previous_month_expenses';
    const DELETE_EXPENSE = '/delete_expense';
    const MONTH_STATISTICS = '/month_statistics';
    const PREVIOUS_MONTH_STATISTICS = '/previous_month_statistics';
}

?>