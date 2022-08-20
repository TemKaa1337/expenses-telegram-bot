<?php declare(strict_types = 1);

namespace App\Services\Validator;

use App\Exception\InvalidBotActionException;
use App\Messages\ErrorMessage;

class RequestValidatorService implements Validator
{
    public function __construct(
        private readonly array $input
    )
    { }

    public function validate(): array
    {
        if (!isset($this->input['message']) || !isset($this->input['message']['text'])) {
            throw new InvalidBotActionException(ErrorMessage::UnknownBotAction->value);
        }
            
        return $this->input;
    }
}