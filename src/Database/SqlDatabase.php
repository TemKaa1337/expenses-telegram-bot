<?php declare(strict_types = 1);

namespace App\Database;

use App\Config\DatabaseConfig;
use PDO;

class SqlDatabase implements Database
{
    private readonly PDO $connection;
    
    public function __construct()
    {
        $dbConfig = new DatabaseConfig();
        $config = $dbConfig->getConfig();

        $this->connection = new PDO("pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}", $config['username'], $config['password']);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function execute(string $query, array $data): array
    {
        $result = $this->connection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result->execute($data);
        $result->setFetchMode(PDO::FETCH_ASSOC);

        return $result->fetchAll();
    }
}

?>