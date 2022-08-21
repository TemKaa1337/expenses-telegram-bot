<?php
declare(strict_types = 1);

namespace App\Http;

use App\Config\BotConfig;

class Response
{
    private const TELEGRAM_URL = 'https://api.telegram.org';

    private readonly string $botKey;

    public function __construct(
        private readonly int $chatId, 
        private readonly string $message, 
        private readonly string $method = 'sendMessage'
    )
    {
        $config = new BotConfig();
        $this->botKey = $config->getConfig()['botKey'];
    }

    public function sendResponse() : array
    {
        $messageLength = mb_strlen($this->message);

        if ($messageLength > 4096) {
            $responseMessages = [];
            $max = (int) ($messageLength / 4096);

            for ($i = 0; $i < $max + 1; $i ++) {
                $slicedMessage = mb_substr($this->message, $i * 4096, 4096);
                $response = new Response(chatId: $this->chatId, message: $slicedMessage);
                $responseMessages[] = $response->sendResponse();
            }

            return $responseMessages;
        }

        $data = ['chat_id' => $this->chatId, 'text' => $this->message];
        return $this->makeRequest(params: $data);
    }

    private function makeRequest(array $params): array
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, self::TELEGRAM_URL."/bot{$this->botKey}/{$this->method}");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

        $response = json_decode(curl_exec($curl), true); 
        curl_close($curl); 
        
        return $response;
    }
}

?>