<?php
declare(strict_types = 1);

namespace App\Command;

use App\Http\Request;
use App\Helper\Helper;
use App\Command\CommandPool;
use App\Expense\Expense;

class Command
{
    private string $command;

    public function __construct(string $command)
    {
        $this->command = $command;
    }

    public function isCommand() : bool
    {
        return Helper::str($this->command)->startsWith('/');
    }

    public function executeCommand(Request $request) : string
    {
        $expenses = new Expense($request);

        switch ($this->command) {
            case CommandPool::START:
                return $this->getAllCommandDescriptions();
            case CommandPool::MONTH_EXPENSES:
                return $expenses->getMonthExpenses();
            case CommandPool::DAY_EXPENSES:
                return $expenses->getDayExpenses();
            case CommandPool::PREVIOUS_MONTH_EXPENSES:
                return $expenses->getPreviousMonthExpenses();
            case CommandPool::DELETE_EXPENSE:
                return $expenses->deleteExpense();
            case CommandPool::MONTH_STATISTICS:
                return $expenses->getMonthExpensesStatistics();
            case CommandPool::PREVIOUS_MONTH_STATISTICS:
                return $expenses->getPreviousMonthExpensesStatistics();
            default:
                return 'Такой команды не существует :(';
        }
    }

    public function getAllCommandDescriptions() : string
    {
        $result = [];
        $descriptions = CommandPool::COMMAND_DESCRIPTIONS;

        foreach ($descriptions as $command => $description) {
            $result[] = "{$command} - {$description}";
        }

        return implode('\n', $result);
    }
}

?>