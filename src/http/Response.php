<?php
declare(strict_types = 1);

namespace App\Http;

use App\Config\BotConfig;
use App\Expense\Expense;
use App\Http\Request;

class Response
{
    public Request $request;
    private BotConfig $config;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->config = new BotConfig();
    }

    public function handleRequest() : void
    {
        if ($this->request->isCommand) {
            $response = $this->request->getCommand()->executeCommand($this->request);
        } else {
            $expense = new Expense($this->request);
            $expense->addExpense();
        }

        $this->sendResponse($response);
    }

    public function sendResponse(array $data = [], string $method = 'sendMessage') : array
    {
        $key = $this->config->getBotKey();
        $curl = curl_init(); 
          
        curl_setopt($curl, CURLOPT_URL, "https://api.telegram.org/bot{$key}/{$method}");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
          
        $response = json_decode(curl_exec($curl), true); 
          
        curl_close($curl); 
          
        return $response;
    }
}

?>