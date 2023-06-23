<?php declare(strict_types=1);

namespace App\Services\Formatters;

final readonly class EachMonthExpensesFormatter
{
    /**
     * @param array $expenses
     * @return string
     */
    public static function format(array $expenses): string
    {
        $formatted = [];
        foreach ($expenses as $expense) {
            $formatted[] = "{$expense['month']}.{$expense['year']} - {$expense['sum']}р.";
        }
        return implode(PHP_EOL, $formatted);
    }
}