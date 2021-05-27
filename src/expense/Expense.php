<?php
declare(strict_types = 1);

namespace App\Expense;

use App\Http\Request;

class Expense
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function addExpense() : string
    {

    }

    public function getMonthExpenses() : string
    {

    }

    public function getDayExpenses() : string
    {
        
    }

    public function getPreviousMonthExpenses() : string
    {
        
    }

    public function deleteExpense() : string
    {
        
    }

    public function getMonthExpensesStatistics() : string
    {
        
    }

    public function getPreviousMonthExpensesStatistics() : string
    {
        
    }

}

?>