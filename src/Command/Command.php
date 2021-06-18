<?php
declare(strict_types = 1);

namespace App\Command;

use App\Exception\InvalidCommandException;
use App\Command\CommandPool;
use App\Expense\Expense;
use App\Helper\Helper;
use App\Http\Request;
use App\Model\User;

class Command
{
    private string $command;
    private Expense $expense;

    public function __construct(string $command, Expense $expense)
    {
        $this->command = $command;
        $this->expense = $expense;
    }

    public function isCommand() : bool
    {
        return Helper::str($this->command)->startsWith('/');
    }

    public function handle() : string
    {
        $isCommand = $this->isCommand();

        if ($isCommand) {
            switch ($this->command) {
                case CommandPool::START: return $this->getAllCommandDescriptions();
                case CommandPool::DAY_EXPENSES: return $this->expense->getDayExpenses();
                case CommandPool::MONTH_EXPENSES: return $this->expense->getMonthExpenses(); 
                case CommandPool::PREVIOUS_MONTH_EXPENSES: return $this->expense->getPreviousMonthExpenses();
                default:
                    if (
                        Helper::str($this->command)->startsWith('/delete') && 
                        strlen($this->command) == 8
                    ) {
                        $expenseId = intval(substr($this->command, -1));

                        if ($expenseId !== 0)
                            return $this->expense->deleteExpense($expenseId);
                    }
    
                    throw new InvalidCommandException('Такой команды не существует :(');
            }
        } else return $this->expense->addExpense();
    }

    public function getAllCommandDescriptions() : string
    {
        $result = [];
        $descriptions = CommandPool::COMMAND_DESCRIPTIONS;

        foreach ($descriptions as $command => $description) {
            $result[] = "{$command} - {$description}";
        }

        return urlencode(implode('\n', $result));
    }
}

?>