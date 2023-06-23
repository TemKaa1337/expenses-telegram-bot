<?php declare(strict_types=1);

namespace App\Services\Commands;

enum Command: string
{
    case Start = '/start';
    case AddExpense = '/add_expense';
    case DayExpenses = '/day_expenses';
    case DeleteExpense = '/delete_expense';
    case AllAliases = '/aliases';
    case SpecificAliases = '/specific_aliases';
    case AddCategoryAlias = '/add_category_alias';
    case AddCategory = '/add_category';
    case DeleteCategoryAlias = '/delete_category_alias';
    case DeleteCategory = '/delete_category';
    case MonthExpensesByCategory = '/month_expenses_by_category';
    case MonthExpenses = '/month_expenses';
    case TotalMonthExpenses = '/total_month_expenses';
    case ExpensesFromDatetime = '/expenses_from_datetime';

    /**
     * @return int
     */
    public function getCommandArgumentNumber(): int
    {
        return match($this) {
            self::Start,
            self::AllAliases,
            self::TotalMonthExpenses,
            self::MonthExpensesByCategory,
            self::DayExpenses,
            self::MonthExpenses => 0,
            self::DeleteExpense,
            self::SpecificAliases,
            self::AddCategory,
            self::DeleteCategory,
            self::ExpensesFromDatetime => 1,
            self::AddExpense,
            self::AddCategoryAlias,
            self::DeleteCategoryAlias => 2
        };
    }

    /**
     * @return class-string
     */
    public function getCommandHandler(): string
    {
        return match($this) {
            self::Start => StartCommand::class,
            self::AddExpense => AddExpenseCommand::class,
            self::DayExpenses => DayExpensesCommand::class,
            self::DeleteExpense => DeleteExpenseCommand::class,
            self::AllAliases => AllCategoryAliasesCommand::class,
            self::SpecificAliases => CategoryAliasesCommand::class,
            self::AddCategoryAlias => AddCategoryAliasCommand::class,
            self::AddCategory => AddCategoryCommand::class,
            self::DeleteCategoryAlias => DeleteCategoryAliasCommand::class,
            self::DeleteCategory => DeleteCategoryCommand::class,
            self::MonthExpensesByCategory => MonthExpensesByCategoryCommand::class,
            self::MonthExpenses => MonthExpensesCommand::class,
            self::TotalMonthExpenses => TotalMonthExpensesCommand::class,
            self::ExpensesFromDatetime => ExpensesFromDateCommand::class,
        };
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return match($this) {
            self::Start => 'Покажет весь доступный функционал с описанием.',
            self::AddExpense => 'Добавить трату.',
            self::MonthExpenses => 'Покажет ваши траты за указанный месяц.',
            self::DayExpenses => 'Покажет ваши траты за указанный день.',
            self::DeleteExpense => 'Удалить трату.',
            self::AllAliases => 'Выведет список алиасов для каждого раздела.',
            self::SpecificAliases => 'Выведет список алиасов для выбранного раздела.',
            self::AddCategory => 'Добавляет новую категорию трат.',
            self::AddCategoryAlias => 'Добавляет псевдоним существующей категории.',
            self::DeleteCategory => 'Позволит удалить категорию.',
            self::DeleteCategoryAlias => 'Позволяет удалить алиас для категории.',
            self::MonthExpensesByCategory => 'Позволяет просмотреть общую сумму трат по каждой категории за указанный месяц.',
            self::TotalMonthExpenses => 'Выведет общее количество потраченных средств за каждый месяц.',
            self::ExpensesFromDatetime => 'Выведет ваши траты начиная с указанной даты.'
        };
    }
}