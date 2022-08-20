<?php declare(strict_types = 1);

namespace App\Config;

use App\Config\Config;

class DatabaseConfig implements Config
{
    private readonly array $config;

    public function __construct()
    {
        $this->config = json_decode(file_get_contents(__DIR__.'/Secret/database_config.json'), true);
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}

?>