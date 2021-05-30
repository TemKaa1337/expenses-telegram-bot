<?php
declare(strict_types = 1);

namespace App\Http;

use App\Config\BotConfig;

class Response
{
    private BotConfig $config;
    private int $chatId;

    public function __construct(int $chatId)
    {
        $this->chatId = $chatId;
        $this->config = new BotConfig();
    }

    public function sendResponse(string $message, string $method = 'sendMessage') : array
    {
        $key = $this->config->getBotKey();
        $curl = curl_init(); 
        $data = ['chat_id' => $this->chatId, 'text' => $message];
          
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