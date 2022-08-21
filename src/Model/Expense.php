<?php declare(strict_types = 1);

namespace App\Model;

use App\Database\Database;
use App\Exception\NoExpenseFoundException;

class Expense
{
    public function __construct(
        private readonly Database $db,
        private readonly User $user,
        private readonly int $expenseId
    ) 
    { }

    public function delete(): void
    {
        $expenseInfo = $this->db->execute('SELECT id FROM expenses WHERE id = ? and user_id = ?', [$this->expenseId, $this->user->getDatabaseUserId()]);
        if (empty($expenseInfo)) {
            throw new NoExpenseFoundException('Такой траты нет.');
        }

        $this->db->execute('DELETE FROM expenses WHERE id = ?', [$this->expenseId]);
    }
}