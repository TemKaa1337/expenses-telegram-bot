<?php declare(strict_types=1);

namespace App\Http;

use function App\Config\config;

final readonly class Response
{
    private const TELEGRAM_URL = 'https://api.telegram.org';

    private string $botKey;

    /**
     * @param int $chatId
     * @param string $message
     * @param string $method
     */
    public function __construct(
        private int $chatId,
        private string $message,
        private string $method = 'sendMessage'
    )
    {
        $this->botKey = config()['bot']['key'];
    }

    /**
     * @return void
     */
    public function send(): void
    {
        $messageLength = mb_strlen($this->message);
        if ($messageLength > 4096) {
            $max = (int) ($messageLength / 4096);
            for ($i = 0; $i < $max + 1; $i ++) {
                $slicedMessage = mb_substr($this->message, $i * 4096, 4096);
                $response = new Response($this->chatId, $slicedMessage);
                $response->send();
            }
        }

        $data = ['chat_id' => $this->chatId, 'text' => $this->message];
        $this->makeRequest($data);
    }

    /**
     * @param array{chat_id: integer, text: string} $params
     * @return void
     */
    private function makeRequest(array $params): void
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::TELEGRAM_URL."/bot{$this->botKey}/{$this->method}");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_close($curl);
    }
}