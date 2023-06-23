<?php declare(strict_types=1);

namespace App\Services\Formatters;

final readonly class IntervalExpensesFormatter
{
    /**
     * @param array $expenses
     * @param string $dateFrom
     * @param bool $skipDayAvg
     * @return string
     */
    public static function format(
        array $expenses,
        string $dateFrom,
        bool $skipDayAvg = false
    ): string
    {
        $formatted = [];
        $total = 0;
        foreach ($expenses as $expense) {
            $date = date('H:i:s', strtotime($expense['created_at']));
            $commandToDelete = "(/delete_expense{$expense['id']})";
            $amountAndCategory = "{$expense['amount']}р, {$expense['category_name']}";
            $note = $expense['note'] !== null ? ", {$expense['note']}." : '';
            $formatted[] = $date.' '.$commandToDelete.' - '.$amountAndCategory.$note;
            $total += $expense['amount'];
        }

        if (!$skipDayAvg) {
            $daysPassedUntilNow = round((time() - strtotime($dateFrom)) / 60 / 60 / 24);
            $avg = number_format($total / $daysPassedUntilNow, 2);
            $formatted[] = "Итого: {$avg}р. в среднем за день.";
        }

        $formatted[] = "Итого: {$total}р.";
        return implode(PHP_EOL, $formatted);
    }
}