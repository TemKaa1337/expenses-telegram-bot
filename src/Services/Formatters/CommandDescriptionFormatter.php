<?php declare(strict_types=1);

namespace App\Services\Formatters;

final readonly class CommandDescriptionFormatter
{
    /**
     * @param array $descriptions
     * @return string
     */
    public static function format(array $descriptions): string
    {
        return implode(PHP_EOL, $descriptions);
    }
}