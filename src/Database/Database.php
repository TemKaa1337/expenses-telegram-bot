<?php
declare(strict_types = 1);

namespace App\Database;

use PDO;
use App\Config\DatabaseConfig;

class Database
{
    private PDO $connection;

    public function __construct()
    {
        $config = new DatabaseConfig();

        $host = $config->getHost();
        $port = $config->getPort();
        $databaseName = $config->getDatabase();
        $username = $config->getUsername();
        $passwdord = $config->getPassword();

        $this->connection = new PDO("pgsql:host=$host;port=$port;dbname=$databaseName", $username, $passwdord);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function execute(string $query, array $data) : array
    {
        $result = $this->connection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result->execute($data);
        $result->setFetchMode(PDO::FETCH_ASSOC);

        return $result->fetchAll();
    }
}

?>