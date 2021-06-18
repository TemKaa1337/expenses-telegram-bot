<?php
declare(strict_types = 1);

namespace App\Expense;

use App\Exception\InvalidInputException;
use App\Categories\Categories;
use App\Database\Database;
use App\Http\Request;
use App\Model\User;
use Exception;

class Expense
{
    private User $user;
    private Database $db;
    private string|float $amount;
    private int $categoryId;

    public function __construct(User $user, Database $db, int $categoryId, string|float $message)
    {
        $this->db = $db;
        $this->user = $user;
        $this->categoryId = $categoryId;
        $this->amount = $message;
    }

    public function addExpense() : string
    {
        $note = $this->getNote($this->amount);
        $this->amount = $this->getAmount($this->amount);
        $this->user->addExpense($this->amount, $this->categoryId, $note);

        return 'Новая трата добавлена успешно!';
    }

    public function getMonthExpenses() : string
    {
        $expenses = $this->user->getMonthExpenses();
        return '';
    }

    public function getDayExpenses() : string
    {
        $expenses = $this->user->getDayExpenses();
        return '';
    }

    public function getPreviousMonthExpenses() : string
    {
        $expenses = $this->user->getPreviousMonthExpenses();
        return '';
    }

    public function deleteExpense(int $expenseId) : string
    {
        $this->user->deleteExpense($expenseId);

        return 'Трата успешно удалена!';
    }

    public function getAmount(string $message) : float
    {
        if (strpos($message, ' ') !== false) {
            $message = explode(' ', $message);
            
            if (is_numeric($message[0])) return floatval($message[0]);
            else throw new InvalidInputException('Неправильный формат суммы.');
        } else throw new InvalidInputException('Неправильный формат сообщения.');
    }

    public function getNote(string $message) : ?string
    {
        if (strpos($message, ' ') !== false) {
            $message = explode(' ', $message);
            
            return $message[2] ?? null;
        } else throw new InvalidInputException('Неправильный формат сообщения.');
    }
}

?>