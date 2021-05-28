<?php
declare(strict_types = 1);

namespace App\Expense;

use App\Categories\Categories;
use App\Database\Database;
use App\Http\Request;
use App\Model\User;

class Expense
{
    private User $user;
    private Database $db;
    private float $amount;
    private string $category;

    public function __construct(string $message, int $requestUserId)
    {
        $categories = new Categories($message);

        $this->db = new Database();
        $this->user = new User($requestUserId, $this->db);
        $this->category = $categories->getCategory();
        $this->amount = $this->getAmount($message);
    }

    public function addExpense() : string
    {
        $this->user->addExpense($this->amount);

        return 'Новая трата добавлена успешно!';
    }

    public function getMonthExpenses() : string
    {
        return '';
    }

    public function getDayExpenses() : string
    {
        return '';
    }

    public function getPreviousMonthExpenses() : string
    {
        return '';
    }

    public function getMonthExpensesStatistics() : string
    {
        return ''; 
    }

    public function getPreviousMonthExpensesStatistics() : string
    {
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
            else throw new \Exception('Неправильный формат суммы');
        } else throw new \Exception('Неправильный формат сообщения');
    }

}

?>