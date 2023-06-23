<?php declare(strict_types=1);

namespace App\Services\Formatters;

final readonly class CategoryAliasesFormatter
{
    /**
     * @param string $categoryName
     * @param array $aliases
     * @return string
     */
    public static function format(string $categoryName, array $aliases): string
    {
        $formatted = ["Список алиасов категории {$categoryName}:"];
        foreach ($aliases as $alias) {
            $formatted[] = ' - '.$alias['alias'];
        }
        return implode(PHP_EOL, $formatted);
    }
}