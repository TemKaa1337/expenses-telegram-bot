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
    private string $option;
    private Expense $expense;
    private User $user;

    public function __construct(string $command, Expense $expense, User $user)
    {
        $this->setCommandInfo($command);
        $this->expense = $expense;
        $this->user = $user;
    }

    private function setCommandInfo(string $command) : void
    {
        if (strpos($command, ' ') !== false) {
            $info = array_map('trim', explode(' ', $command));

            if (count($info) > 2) {
                $command = array_shift($info);
                $option = implode(' ', $info);
            } else {
                [$command, $option] = $info;
            }

            $this->command = $command;
            $this->option = $option;
        } else {
            if (strpos($command, CommandPool::DELETE_CATEGORY) !== false)
                [$this->command, $this->option] = [CommandPool::DELETE_CATEGORY, str_replace(CommandPool::DELETE_CATEGORY, '', $command)];
            else if (strpos($command, CommandPool::DELETE_EXPENSE) !== false)
                [$this->command, $this->option] = [CommandPool::DELETE_EXPENSE, str_replace(CommandPool::DELETE_EXPENSE, '', $command)];
            else
                [$this->command, $this->option] = [$command, ''];
        }
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
                case CommandPool::MONTH_EXPENSES: return $this->expense->getMonthExpenses($this->option); 
                case CommandPool::MONTH_EXPENSES_BY_CATEGORY: return $this->expense->getMonthExpensesByCategory($this->option);
                case CommandPool::PREVIOUS_MONTH_EXPENSES: return $this->expense->getPreviousMonthExpenses();
                case CommandPool::ALIASES:
                    $categories = new Categories('', new Database());
                    return $categories->getListOfAllAliases($this->user->getUserId());
                case CommandPool::DELETE_CATEGORY:
                    if ($this->option !== '') {
                        $categoryId = intval($this->option);
                        $category = new Categories($this->command, new Database());
    
                        if ($categoryId !== 0 && $category->isUserAllowedToDeleteCategory($this->user->getUserId(), $categoryId))
                            return $category->deleteCategory($categoryId);
                    }

                    return 'Неправильный номер категории!';
                case CommandPool::DELETE_EXPENSE:
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

        $result[] = 'Для того, чтобы добавить трату вводите в формате: {сумма траты (например, 14.1)} {название или алиас раздела} {примечание}.';
        $result[] = 'Пример: 14.5 продукты ничего тольком не купил';
        $result[] = 'Для того, чтобы добавить категорию расходов, введите данные в формате: /add_category {CategoryName}.';
        $result[] = 'Пример: /add_category Бензин';
        $result[] = 'Для того, чтобы добавить алиас для категории расходов, введите данные в формате: /add_category_alias {CategoryName} {Alias}.';
        $result[] = 'Пример: /add_category_alias Бензин бенз (важно, что слово, стоящее сразу после команды /add_category_alias, должно быть таким же по написанию, как вы добавляли через /add_category)';

        return implode(PHP_EOL, $result);
    }
}

?>