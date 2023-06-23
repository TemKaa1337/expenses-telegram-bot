<?php declare(strict_types=1);

namespace App\Services\Validators;

use App\Exceptions\InvalidBotActionException;
use App\Messages\Error;

final readonly class RequestValidator
{
    /**
     * @param array<string, mixed> $input
     */
    public function __construct(
        private array $input
    )
    {}

    /**
     * @throws InvalidBotActionException
     * @return array<string, mixed>
     */
    public function validate(): array
    {
        if (!isset($this->input['message']) || !isset($this->input['message']['text'])) {
            throw new InvalidBotActionException(Error::UnknownBotAction->value);
        }
        return $this->input;
    }
}