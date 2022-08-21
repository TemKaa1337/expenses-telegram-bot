<?php declare(strict_types = 1);

namespace App\Logger;

use App\Database\Database;

class Logger
{
    public function __construct(private readonly Database $db) {}

    public function info(int $chatId, string $type, array $message): void
    {
        $sql = 'INSERT INTO log (chat_id, type, message, created_at) VALUES (?, ?, ?, ?)';
        $bindings = [$chatId, $type, json_encode($message), date('Y-m-d H:i:s')];
        $this->db->execute($sql, $bindings);
    }

    public function error(\Exception $error): void
    {
        $sql = 'INSERT INTO exception_logging (stack_trace, message, file, line, created_at) VALUES (?, ?, ?, ?, ?)';
        $bindings = [$error->getTraceAsString(), $error->getMessage(), $error->getFile(), $error->getLine(), date('Y-m-d H:i:s')];
        $this->db->execute($sql, $bindings);
    }
}

?>