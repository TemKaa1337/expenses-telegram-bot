<?php
declare(strict_types = 1);

namespace App\Command;

class CommandPool
{
    const START = '/start';
    const MONTH_EXPENSES = '/month_expenses';
    const DAY_EXPENSES = '/day_expenses';
    const PREVIOUS_MONTH_EXPENSES = '/previous_month_expenses';

    const COMMAND_DESCRIPTIONS = [
        self::START => 'Это команда вам покажет весь доступный функционал.',
        self::MONTH_EXPENSES => 'Это команда вам покажет ваши траты за текущий месяц',
        self::DAY_EXPENSES => 'Это команда вам покажет ваши траты за текущий день',
        self::PREVIOUS_MONTH_EXPENSES => 'Это команда вам покажет ваши траты за предыдущий месяц'
    ];
}

?>