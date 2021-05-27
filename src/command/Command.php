<?php
declare(strict_types = 1);

namespace App\Command;

use App\Helper\Helper;
use App\Command\CommandPool;

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
        switch ($this->command) {
            case CommandPool::START:
                break;
            case CommandPool::MONTH_EXPENSES:
                break;
            case CommandPool::DAY_EXPENSES:
                break;
            case CommandPool::PREVIOUS_MONTH_EXPENSES:
                break;
            case CommandPool::DELETE_EXPENSE:
                break;
            case CommandPool::MONTH_STATISTICS:
                break;
            case CommandPool::PREVIOUS_MONTH_STATISTICS:
                break;
        }
    }
}

?>