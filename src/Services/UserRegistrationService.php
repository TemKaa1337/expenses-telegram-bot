<?php declare(strict_types = 1);

namespace App\Services;

use App\Database\Database;

class UserRegistrationService
{
    public function __construct(
        private readonly Database $db,
        private readonly int $telegramUserId,
        private readonly string $firstName
    ) 
    { }

    public function registrateIfUnregistered(): void
    {
        $userDoesntExist = empty($this->db->execute('SELECT id FROM users WHERE telegram_id = ?', [$this->telegramUserId]));
        if ($userDoesntExist) {
            $query = 'INSERT INTO users (telegram_id, first_name) VALUES (?, ?)';
            $this->db->execute($query, [$this->telegramUserId, $this->firstName]);
        }
    }
}