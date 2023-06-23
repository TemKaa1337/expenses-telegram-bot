<?php declare(strict_types=1);

namespace App\Services;

use App\Utils\Singleton;
use PDO;
use function App\Config\config;

final class Database extends Singleton
{
    private readonly PDO $connection;

    protected function __construct()
    {
        $config = config()['database'];
        $this->connection = new PDO("pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}", $config['username'], $config['password']);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param string $query
     * @param list<mixed> $bindings
     * @return array
     */
    public function execute(string $query, array $bindings): array
    {
        $result = $this->connection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result->execute($bindings);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        return $result->fetchAll();
    }
}