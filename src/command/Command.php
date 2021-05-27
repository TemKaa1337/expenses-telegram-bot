<?php
declare(strict_types = 1);

namespace App\Command;

use App\Helper\Helper;

class Command
{
    private string $command;

    public function __construct(string $command)
    {
        $this->command = $command;
    }

    public function isCommand() : bool
    {
        return Helper::str($this->command)->startsWith('/');
    }

    public function executeCommand() : void
    {

    }
}

?>