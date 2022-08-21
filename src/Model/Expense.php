<?php declare(strict_types = 1);

namespace App\Model;

use App\Database\Database;
use App\Exception\NoExpenseFoundException;
use App\Messages\ErrorMessage;

class Expense
{
    public function __construct(
        private readonly Database $db,
        private readonly int $userId,
        private readonly int $expenseId
    ) 
    { }

    public function delete(): void
    {
        $expenseInfo = $this->db->execute('SELECT id FROM expenses WHERE id = ? and user_id = ?', [$this->expenseId, $this->userId]);
        if (empty($expenseInfo)) {
            throw new NoExpenseFoundException(ErrorMessage::NoSuchExpense->value);
        }

        $this->db->execute('DELETE FROM expenses WHERE id = ?', [$this->expenseId]);
    }
}