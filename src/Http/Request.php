<?php
declare(strict_types = 1);

namespace App\Http;

class Request
{
    protected int $chatId;
    protected int $userId;
    protected string $message;
    protected string $firstName;
    protected string $secondName;

    public function __construct()
    {
        $input = file_get_contents('php://input'); 
        $input = json_decode($input, true);

        $this->chatId = $input['message']['chat']['id'];
        $this->userId = $input['message']['from']['id'];
        $this->message = $input['message']['text'];
    }

    public function getChatId() : int
    {
        return $this->chatId;
    }

    public function getUserId() : int
    {
        return $this->userId;
    }

    public function getMessage() : string
    {
        return $this->message;
    }

    public function getFirstName() : string
    {
        return '';
    }

    public function getSecondName() : string
    {
        return '';
    }
}

?>