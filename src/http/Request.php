<?php
declare(strict_types = 1);

namespace App\Http;

use App\Command\Command;

class Request
{
    private int $chatId;
    private int $userId;
    private string $message;
    private array $location;
    private bool $isCommand;
    private Command $command;

    public function __construct()
    {
        $input = file_get_contents('php://input'); 
        $input = json_decode($input, true);

        $this->chatId = $input['message']['chat']['id'];
        $this->userId = $input['message']['from']['id'];
        $this->message = $input['message']['text'];
        $this->location = [$input['message']['location']['latitude'], $input['message']['location']['longitude']];

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
}

?>