<?php
declare(strict_types = 1);

namespace App\Database;

use App\Config\DatabaseConfig;

class Database
{
    private $connection;

    public function __construct()
    {
        $config = new DatabaseConfig();
        //TODO make and store database connection
    }
}

?>