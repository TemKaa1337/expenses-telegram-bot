<?php
declare(strict_types = 1);

namespace App\Http;

class Request
{
    public int $chatId;
    public int $userId;
    public string $message;
    public string $firstName;
    public string $secondName;
    public array $input;

    public function __construct()
    {
        $input = file_get_contents('php://input'); 
        $input = json_decode($input, true);

        $this->input = $input;
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

    public function getInput() : array
    {
        return $this->input;
    }
}

?>