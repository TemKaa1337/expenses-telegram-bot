<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Exception\InvalidBotActionException;
use App\Services\Validator\RequestValidatorService;

final class RequestValidatorTest extends TestCase 
{
    private function getInputWithMessage(string $message): array
    {
        return [
            'update_id' => 836780966,
            'message' => [
                'from' => [
                    'id' => 772517840,
                    'is_bot' => false,
                    'first_name' => 'Artem',
                    'username' => 'temkaawow',
                    'language_code' => 'ru'
                ],
                'chat' => [
                    'id' => 772517840,
                    'first_name' => 'Artem',
                    'username' => 'temkaawow',
                    'type' => 'private'
                ],
                'date' => strtotime(date('Y-m-d H:i:s')),
                'text' => $message
            ]
        ];
    }

    private function getInputWithoutMessage(): array
    {
        return [
            'update_id' => 836780966,
            'message' => [
                'from' => [
                    'id' => 772517840,
                    'is_bot' => false,
                    'first_name' => 'Artem',
                    'username' => 'temkaawow',
                    'language_code' => 'ru'
                ],
                'chat' => [
                    'id' => 772517840,
                    'first_name' => 'Artem',
                    'username' => 'temkaawow',
                    'type' => 'private'
                ],
                'date' => strtotime(date('Y-m-d H:i:s'))
            ]
        ];
    }

    public function testInputWithoutMessageField(): void
    {
        $input = $this->getInputWithoutMessage();

        $requestValidator = new RequestValidatorService(input: $input);
        $this->expectException(InvalidBotActionException::class);
        $requestValidator->validate();
    }

    public function testInputWithMessageField(): void
    {
        $input = $this->getInputWithMessage('/start');

        $requestValidator = new RequestValidatorService(input: $input);
        $result = $requestValidator->validate();
        $this->assertEquals($input, $result);
    }
}