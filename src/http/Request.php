<?php
declare(strict_types = 1);

namespace App\Http;

use App\Command\Command;

class Request
{
    private int $chatId;
    private int $userId;
    private string $message;
    private bool $isCommand;
    private Command $command;

    public function __construct()
    {
        $input = file_get_contents('php://input'); 
        $input = json_decode($input, true);

        $this->chatId = $input['message']['chat']['id'];
        $this->userId = $input['message']['from']['id'];
        $this->message = $input['message']['text'];

        $command = new Command($this->message);
        $this->isCommand = $command->isCommand();

        if ($this->isCommand) {
            $this->command = $command;
        }
    }

    public function getCommand() : Command
    {
        return $this->command;
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