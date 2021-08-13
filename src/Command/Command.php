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
    private string $option = '';
    private Expense $expense;
    private User $user;

    public function __construct(string $command, Expense $expense, User $user)
    {
        [$command, $option] = strpos($command, ' ') !== false ? explode(' ', $command) : [$command, ''];
        $this->command = trim($command);
        $this->option = trim($option);
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
                case CommandPool::DELETE_CATEGORY:
                    $this->option = str_replace(CommandPool::DELETE_CATEGORY, '', $this->command);

                    if ($this->option !== '' || $this->option < 3) {
                        $categoryId = intval(str_replace(CommandPool::DELETE_CATEGORY, '', $this->command));
                        $category = new Categories($this->command, new Database());
    
                        if ($categoryId !== 0 && $category->isUserAllowedToDeleteCategory($this->user->getUserId(), $categoryId))
                            return $category->deleteCategory($categoryId);
                    }

                    return 'Неправильный номер категории!';
                case CommandPool::DELETE_EXPENSE:
                    $this->option = str_replace(CommandPool::DELETE_EXPENSE, '', $this->command);

                    if ($this->option !== '') {
                        $expenseId = intval($this->option);

                        if ($expenseId !== 0 && $this->expense->isUserAllowedToDeleteExpense($expenseId))
                            return $this->expense->deleteExpense($expenseId);
                    }

                    return 'Неправильный номер траты!';
                case CommandPool::ADD_CATEGORY_ALIAS:
                    if ($this->option !== '') {
                        $category = new Categories($this->command.' '.$this->option, new Database());
                        return $category->addCategoryAlias();
                    } else return 'Не хватает параметров :(';
                case CommandPool::ADD_CATEGORY:
                    if ($this->option !== '') {
                        $category = new Categories($this->command.' '.$this->option, new Database());
                        return $category->addCategory($this->user->getUserId());
                    } else return 'Не хватает параметров :(';
            }
        } else return $this->expense->addExpense();

        throw new InvalidCommandException('Такой команды не существует или она введена неверно :(');
    }

    public function getAllCommandDescriptions() : string
    {
        $result = [];
        $descriptions = CommandPool::COMMAND_DESCRIPTIONS;

        foreach ($descriptions as $command => $description) {
            $result[] = "{$command} - {$description}";
        }

        $result[] = 'Для того, чтобы добавить трату вводите в формате: {сумма траты (например, 14.1)} {название или алиас раздела} {примечание}';
        $result[] = 'Пример: 14.5 продукты ничего тольком не купил';

        return implode(PHP_EOL, $result);
    }
}

?>