<?php declare(strict_types = 1);

namespace App\Services\Validator;

use App\Exception\InvalidCommandException;
use App\Command\Command;
use App\Messages\ErrorMessage;

class CommandValidatorService implements Validator
{
    public function __construct(
        private readonly string $command
    )
    { }

    public function validate(): array
    {
        $arguments = [];
        $command = $this->command;

        if (strpos($command, ' ') !== false) {
            $explodedMessage = explode(' ', $command);

            if (is_numeric($explodedMessage[0])) {
                $command = Command::AddExpense->value;
                $arguments = $explodedMessage;
            } else {
                $command = array_shift($explodedMessage);
                $arguments = $explodedMessage;
            }

            if (count($arguments) >= 4) {
                $temp = array_slice($arguments, 0, 2);
                $temp[] = implode(
                    ' ', 
                    array_slice($arguments, 2)
                );
                $arguments = $temp;
            }
        } else {
            if (is_numeric($command)) {
                $arguments = [$command];
                $command = Command::AddExpense->value;
            }
        }

        if (!is_numeric($command)) {
            $commandLength = strlen($command);
            if (is_numeric($command[$commandLength - 1])) {
                $lastNumericCharIndex = $commandLength - 1;

                for ($i = $commandLength - 1; $i >= 0; $i --) {
                    if (!is_numeric($command[$i])) break;
                    $lastNumericCharIndex = $i;
                }

                $arguments[] = substr($command, $lastNumericCharIndex, $commandLength - $lastNumericCharIndex);
                $command = substr($command, 0, $lastNumericCharIndex);
            }

            try {
                $userCommand = Command::from($command);
            } catch (\ValueError $e) {
                throw new InvalidCommandException(ErrorMessage::UnknownCommand->value);
            }
        }

        $command = $userCommand ?? Command::AddExpense;
        $this->validateCommandArgumentsNumber($command, count($arguments));

        return [
            'command' => $command,
            'arguments' => $arguments
        ];
    }

    private function validateCommandArgumentsNumber(Command $command, int $count): void
    {
        if ($count < $command->getCommandArgumentNumber()) {
            throw new InvalidCommandException(ErrorMessage::InsufficientCommandArguments->value);
        }
    }
}