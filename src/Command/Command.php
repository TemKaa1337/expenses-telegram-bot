<?php declare(strict_types = 1);

namespace App\Command;

enum Command: string
{
    case Start = '/start';
    case AddExpense = '/add_expense';
    case MonthExpenses = '/month_expenses';
    case DayExpenses = '/day_expenses';
    case DeleteExpense = '/delete_expense';
    case AllAliases = '/aliases';
    case SpecificAliases = '/specific_aliases';
    case AddCategory = '/add_category';
    case AddCategoryAlias = '/add_category_alias';
    case DeleteCategory = '/delete_category';
    case DeleteCategoryAlias = '/delete_category_alias';
    case MonthExpensesByCategory = '/month_expenses_by_category';
    case AverageEachMonthExpenses = '/average_each_month_expenses';
    case TotalMonthExpenses = '/total_month_expenses';
    case ExpensesFromDatetime = '/expenses_from_datetime';

    public function getDescription(): string
    {
        return match($this) {
            $this::Start => 'Покажет весь доступный функционал с описанием.',
            $this::AddExpense => 'Добавить трату.',
            $this::MonthExpenses => 'Покажет ваши траты за указанный месяц.',
            $this::DayExpenses => 'Покажет ваши траты за указанный день.',
            $this::DeleteExpense => 'Удалить трату.',
            $this::AllAliases => 'Выведет список алиасов для каждого раздела.',
            $this::SpecificAliases => 'Выведет список алиасов для выбранного раздела.',
            $this::AddCategory => 'Добавляет новую категорию трат.',
            $this::AddCategoryAlias => 'Добавляет псевдоним существующей категории.',
            $this::DeleteCategory => 'Позволит удалить категорию.',
            $this::DeleteCategoryAlias => 'Позволяет удалить алиас для категории.',
            $this::MonthExpensesByCategory => 'Позволяет просмотреть общую сумму трат по каждой категории за указанный месяц.',
            $this::AverageEachMonthExpenses => 'Позволяет просмотреть среднее значение расходов по каждой категории за каждый месяц.',
            $this::TotalMonthExpenses => 'Выведет общее количество потраченных средств за каждый месяц.',
            $this::ExpensesFromDatetime => 'Выведет ваши траты начиная с указанной даты.'
        };
    }

    public function getCommandArgumentNumber(): int
    {
        return match($this) {
            $this::Start => 0,
            $this::AddExpense => 2,
            $this::DeleteExpense => 1,
            $this::AllAliases => 0,
            $this::SpecificAliases => 1,
            $this::AddCategory => 1,
            $this::AddCategoryAlias => 2,
            $this::DeleteCategory => 1,
            $this::DeleteCategoryAlias => 2,
            $this::AverageEachMonthExpenses => 0,
            $this::TotalMonthExpenses => 0,
            // d || d.m || d.m.Y
            $this::ExpensesFromDatetime => 1,
            // null || d || d.m || d.m.Y 
            $this::DayExpenses => 0,
            // null || m || m.Y
            $this::MonthExpenses => 0,
            // null || m || m.Y 
            $this::MonthExpensesByCategory => 0
        };
    }
}

?>