<?php
declare(strict_types = 1);

namespace App\Expense;

use App\Categories\Categories;
use App\Database\Database;
use App\Http\Request;
use App\Model\User;
use Exception;

class Expense
{
    private User $user;
    private Database $db;
    private float $amount;
    private int $categoryId;

    public function __construct(Request $request)
    {
        $this->db = new Database();
        $categories = new Categories($request->getMessage(), $this->db);

        $this->user = new User($request, false, $this->db);
        $this->categoryId = $categories->getCategoryId();
        $this->amount = $this->getAmount($request->getMessage());
    }

    public function addExpense() : string
    {
        $this->user->addExpense($this->amount, $this->categoryId);

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
            else throw new Exception('Неправильный формат суммы');
        } else throw new Exception('Неправильный формат сообщения');
    }

}

?>