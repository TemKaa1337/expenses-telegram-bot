<?php declare(strict_types=1);

namespace App\Services\Formatters;

final readonly class ExpensesByCategoryFormatter
{
    /**
     * @param array $expenses
     * @return string
     */
    public static function format(array $expenses): string
    {
        $total = 0;
        $grouped = [];
        $formatted = [];
        foreach ($expenses as $expense) {
            if (isset($grouped[$expense['category_name']])) {
                $grouped[$expense['category_name']] += (float) $expense['amount'];
            } else {
                $grouped[$expense['category_name']] = (float) $expense['amount'];
            }
        }

        foreach ($grouped as $category => $value) {
            $formatted[] = "{$category}: {$value}р.";
            $total += $value;
        }

        $total = round($total, 2);
        $formatted[] = "Итого: {$total}р.";
        return implode(PHP_EOL, $formatted);
    }
}