<?php
declare(strict_types = 1);

namespace App\Expense;

use App\Http\Request;
use App\Database\Database;

class Expense
{
    private Request $request;
    private Database $db;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->db = new Database();
    }

    public function addExpense() : string
    {
        return '';
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

    public function deleteExpense() : string
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

}

?>