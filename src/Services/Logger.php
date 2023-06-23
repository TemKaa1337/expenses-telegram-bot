<?php declare(strict_types=1);

namespace App\Services;

use Exception;

final readonly class Logger
{
    /**
     * @param Database $db
     */
    public function __construct(
        private Database $db
    ) {}

    /**
     * @param Exception $e
     * @return void
     */
    public function error(Exception $e): void
    {
        $this->db->execute(
            <<<SQL
                INSERT INTO exception_logging (stack_trace, message, file, line, created_at) VALUES (?, ?, ?, ?, ?)
            SQL,
            [$e->getTraceAsString(), $e->getMessage(), $e->getFile(), $e->getLine(), date('Y-m-d H:i:s')]
        );
    }
}