<?php
declare(strict_types = 1);

namespace App\Http;

use App\Database\Database;
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
        $messageLength = strlen($message);

        if ($messageLength > 4096) {
            $result = [];
            $max = (int) ($messageLength / 4096);

            for ($i = 0; $i < $max + 1; $i ++) {
                $result[] = $this->sendResponse(substr($message, $i * 4096, 4096));
            }

            return $result;
        }

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

    public function logResponse(Database $db, array $response): void
    {
        $db->execute('INSERT INTO response_logging (response, created_at) VALUES (?, ?)', [json_encode($response), date('Y-m-d H:i:s')]);
    }
}

?>