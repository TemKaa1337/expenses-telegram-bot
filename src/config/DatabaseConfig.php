<?php
declare(strict_types = 1);

namespace App\Config;

class DatabaseConfig
{
    private string $host;
    private int $port;
    private string $database;
    private string $username;
    private string $password;

    public function __construct()
    {
        $databaseInfo = json_decode(file_get_contents(__DIR__.'/Secret/database_config.json'), true);

        $this->host = $databaseInfo['host'];
        $this->port = $databaseInfo['port'];
        $this->database = $databaseInfo['database'];
        $this->username = $databaseInfo['username'];
        $this->password = $databaseInfo['password'];
    }

    public function getHost() : string
    {
        return $this->host;
    }

    public function getPort() : int
    {
        return $this->port;
    }

    public function getDatabase() : string
    {
        return $this->database;
    }

    public function getUsername() : string
    {
        return $this->username;
    }

    public function getPassword() : string
    {
        return $this->password;
    }
}

?>