<?php
declare(strict_types = 1);

namespace App\Command;

class CommandPool
{
    const START = '/start';
    const MONTH_EXPENSES = '/month_expenses';
    const DAY_EXPENSES = '/day_expenses';
    const PREVIOUS_MONTH_EXPENSES = '/previous_month_expenses';
    const DELETE_EXPENSE = '/delete';
    const ALIASES = '/aliases';
    const ADD_CATEGORY = '/add_category';
    const ADD_CATEGORY_ALIAS = '/add_category_alias';

    const COMMAND_DESCRIPTIONS = [
        self::START => 'Покажет весь доступный функционал.',
        self::MONTH_EXPENSES => 'Покажет ваши траты за текущий месяц',
        self::DAY_EXPENSES => 'Покажет ваши траты за текущий день',
        self::PREVIOUS_MONTH_EXPENSES => 'Покажет ваши траты за предыдущий месяц',
        self::DELETE_EXPENSE => 'Позволит удалить трату, в начале каждой траты указывается какой командой это можно сделать',
        self::ALIASES => 'Выводит список алиасов для каждого раздела',
        self::ADD_CATEGORY => 'Добавляет новую категорию трат',
        self::ADD_CATEGORY_ALIAS => 'Добавляет псевдоним существующей категории'
    ];
}

?>