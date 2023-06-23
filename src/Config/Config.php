<?php declare(strict_types=1);

namespace App\Config;

if (!function_exists('config')) {
    /**
     * @return array{
     *     database: array{
     *         host: string,
     *         port: integer,
     *         database: string,
     *         username: string,
     *         password: string
     *     },
     *     bot: array<string, mixed>
     * }
     */
    function config(): array {
        $databaseConfig = json_decode(file_get_contents(__DIR__.'/database.json'), true);
        $botConfig = json_decode(file_get_contents(__DIR__.'/bot.json'), true);
        return [
            'database' => $databaseConfig,
            'bot' => $botConfig
        ];
    }
}