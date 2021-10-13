<?php
declare(strict_types = 1);

namespace App\Http;

class Request
{
    public int $chatId;
    public int $userId;
    public string $message;
    public string $firstName;
    public array $input;

    public function __construct()
    {
        $input = file_get_contents('php://input'); 
        $input = json_decode($input, true);

        if (!isset($input['message'])) die();
        
        $this->input = $input;
        $this->chatId = $input['message']['chat']['id'];
        $this->userId = $input['message']['from']['id'];
        $this->message = $this->formatMessage($input['message']['text']);
        $this->firstName = $input['message']['from']['first_name'];
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
        return $this->firstName;
    }

    public function getInput() : array
    {
        return $this->input;
    }

    protected function formatMessage(string $message) : string
    {
        return str_replace('\\', '', $message);
    }
}

?>