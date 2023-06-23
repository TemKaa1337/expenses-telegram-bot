<?php declare(strict_types=1);

namespace App\Http;

final readonly class Request
{
    public int $userId;
    public int $chatId;
    public string $message;
    public string $firstName;

    /**
     * @param array $input
     */
    public function __construct(
        array $input
    )
    {
        $this->userId = $input['message']['from']['id'];
        $this->chatId = $input['message']['chat']['id'];
        $this->message = $input['message']['text'];
        $this->firstName = $input['message']['from']['first_name'];
    }
}