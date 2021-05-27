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

    public function addExpense() : void
    {
        
    }
}

?>