<?php
declare(strict_types = 1);

namespace App\Command;

use App\Categories\Categories;
use App\Exception\InvalidCommandException;
use App\Command\CommandPool;
use App\Database\Database;
use App\Expense\Expense;
use App\Helper\Helper;
use App\Http\Request;
use App\Model\User;

class Command
{
    private string $command;
    private Expense $expense;
    private User $user;

    public function __construct(string $command, Expense $expense, User $user)
    {
        $this->command = $command;
        $this->expense = $expense;
        $this->user = $user;
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
                case CommandPool::ALIASES:
                    $categories = new Categories('', new Database());
                    return $categories->getListOfAllAliases($this->user->getUserId());
                default:
                    if (
                        Helper::str($this->command)->startsWith('/delete') && 
                        strlen($this->command) == 8
                    ) {
                        $expenseId = intval(substr($this->command, -1));

                        if ($expenseId !== 0 && $this->expense->isUserAllowedToDeleteExpense($expenseId))
                            return $this->expense->deleteExpense($expenseId);
                        else return 'Неправильный номер траты!';
                    } else if (Helper::str($this->command)->startsWith('/add_category_alias')) {
                        if (strpos($this->command, ' ') === false) return 'Не хватает параметров :(';
                        
                        $categories = new Categories($this->command, new Database());
                        return $categories->addCategoryAlias();
                    } else if (Helper::str($this->command)->startsWith('/add_category')) {
                        if (strpos($this->command, ' ') === false) return 'Не хватает параметров :(';

                        $categories = new Categories($this->command, new Database());
                        return $categories->addCategory($this->user->getUserId());
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

        $result[] = 'Для того, чтобы добавть трату вводите в формате: {сумма траты (например, 14.1)} {название или алиас раздела} {примечание}';
        $result[] = 'Пример: 14.5 продукты ничего тольком не купил(-а)';

        return implode(PHP_EOL, $result);
    }
}

?>