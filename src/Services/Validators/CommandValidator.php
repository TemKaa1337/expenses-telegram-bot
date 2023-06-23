<?php declare(strict_types=1);

namespace App\Services\Validators;

use App\Exceptions\InvalidCommandException;
use App\Messages\Error;
use App\Services\Commands\Command;

final readonly class CommandValidator
{
    /**
     * @param string $text
     */
    public function __construct(
        private string $text
    )
    {}

    /**
     * @return array{command: Command, arguments: array<int<0, max>, string>}
     * @throws InvalidCommandException
     */
    public function validate(): array
    {
        $command = $this->getCommand();
        $arguments = $this->formatCommandArguments($command);
        $argumentsCount = count($arguments);
        $this->validateCommandArgumentsNumber($command, $argumentsCount);
        $this->validateCommandArgumentsType($command, $arguments);
        return ['command' => $command, 'arguments' => $arguments];
    }

    /**
     * @param Command $command
     * @return array<int<0, max>, string>
     */
    private function formatCommandArguments(Command $command): array
    {
        $arguments = array_filter(
            explode(' ', trim(str_replace($command->value, '', $this->text))),
            fn (string $elem): bool => $elem !== ''
        );
        if (count($arguments) < 3) {
            return $arguments;
        }

        $sliced = array_slice($arguments, 0, 2);
        $sliced[] = implode(
            ' ',
            array_slice($arguments, 2)
        );
        return $sliced;
    }

    /**
     * @return Command
     */
    private function getCommand(): Command
    {
        $commands = Command::cases();
        foreach ($commands as $command) {
            if (str_starts_with($this->text, $command->value)) {
                return $command;
            }
        }

        return Command::AddExpense;
    }

    /**
     * @param Command $command
     * @param int $argumentsCount
     * @return void
     * @throws InvalidCommandException
     */
    private function validateCommandArgumentsNumber(Command $command, int $argumentsCount): void
    {
        if ($argumentsCount < $command->getCommandArgumentNumber()) {
            throw new InvalidCommandException(Error::InsufficientCommandArguments->value);
        }
    }

    /**
     * @param Command $command
     * @param array<int<0, max>, string> $arguments
     * @return void
     * @throws InvalidCommandException
     */
    private function validateCommandArgumentsType(Command $command, array $arguments): void
    {
        if ($command === Command::AddExpense || $command === Command::DeleteExpense) {
            if (!is_numeric($arguments[0])) {
                throw new InvalidCommandException(Error::IncorrectNumberFormat->value);
            }
        }
    }
}