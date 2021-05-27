<?php
declare(strict_types = 1);

namespace App\Http;

use App\Config\Config;
use App\Expense\Expense;
use App\Http\Request;

class Response
{
    public Request $request;
    private Config $config;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->config = new Config();
    }

    public function handleRequest() : void
    {
        if ($this->request->isCommand) {
            $this->request->getCommand()->executeCommand();
        }

        $expense = new Expense($this->request);
        $expense->addExpense();
    }

    public function sendResponse() : void
    {

    }
}

?>