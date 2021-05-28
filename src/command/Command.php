<?php
declare(strict_types = 1);

namespace App\Command;

use App\Http\Request;
use App\Helper\Helper;
use App\Command\CommandPool;
use App\Expense\Expense;
use App\Model\User;

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
            case CommandPool::START: return $this->getAllCommandDescriptions($request);
            case CommandPool::DAY_EXPENSES: return $expenses->getDayExpenses();
            case CommandPool::MONTH_EXPENSES: return $expenses->getMonthExpenses(); 
            case CommandPool::PREVIOUS_MONTH_EXPENSES: return $expenses->getPreviousMonthExpenses();
            default:
                if (
                    Helper::str($this->command)->startsWith('/delete') && 
                    strlen($this->command) == 8
                )
                    return $expenses->deleteExpense(intval(substr($this->command, -1)));

                return 'Такой команды не существует :(';
        }
    }

    public function getAllCommandDescriptions(Request $request) : string
    {
        $result = [];
        $descriptions = CommandPool::COMMAND_DESCRIPTIONS;

        $user = new User($request, true);

        foreach ($descriptions as $command => $description) {
            $result[] = "{$command} - {$description}";
        }

        return implode('\n', $result);
    }
}

?>