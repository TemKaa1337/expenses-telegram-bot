<?php
declare(strict_types = 1);

namespace App\Database;

use App\Config\DatabaseConfig;

class Database
{
    public function __construct()
    {
        $config = new DatabaseConfig();
    }
}

?>