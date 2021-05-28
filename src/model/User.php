<?php
declare(strict_types = 1);

namespace App\Model;

use App\Database\Database;

class User
{
    private int $requestUserId;
    private Database $db;

    public function __construct(int $requestUserId, Database $db = new Database())
    {
        $this->requestUserId = $requestUserId;
        $this->db = $db;
    }

    public function addUserExpense()
    {
        
    }

    public function createUserIfNeeded(string $firstName, string $secondName) : void
    {
        //TODO if user doesnt exist than need to add
        $sql = "SELECT id FROM users WHERE telegram_id = {$this->requestUserId}";
        $sql2 = "INSERT INTO users () VALUES ()";
    }

    public function addExpense(float $amount) : void
    {
        $userId = 1;
        $query = "INSERT INTO expenses (created_at, amount, user_id) VALUES (".date('Y-m-d H:i:s', strtotime('+3 hours')).", {$amount}, {$userId})";
    }

    public function deleteExpense(int $expenseId) : void
    {
        $query = "DELETE FROM expenses WHERE id = {$expenseId} AND telegram_id = {$this->requestUserId}";
    }
}

?>