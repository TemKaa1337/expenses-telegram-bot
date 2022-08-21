<?php declare(strict_types = 1);

namespace App\Model;

use App\Database\Database;

class User
{
    private readonly int $databaseUserId;

    public function __construct(
        private readonly Database $db,
        private readonly int $telegramUserId,
        private readonly string $firstName
    )
    {
        $this->setUserInfo();
    }

    private function setUserInfo(): void
    { 
        $userInfo = $this->db->execute('SELECT * FROM users WHERE telegram_user_id = ?', [$this->telegramUserId]);
        $this->databaseUserId = $userInfo[0]['id'];
    }

    public function getDatabaseUserId(): int
    {
        return $this->databaseUserId;
    }
}

?>