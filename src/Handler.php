<?php declare(strict_types = 1);

namespace App;

use App\Exceptions\CategoryAliasAlreadyExistsException;
use App\Exceptions\CategoryAliasNotFoundException;
use App\Exceptions\CategoryAlreadyExistException;
use App\Exceptions\CategoryNotFoundException;
use App\Exceptions\ExpenseNotFoundException;
use App\Exceptions\InvalidBotActionException;
use App\Exceptions\InvalidCommandException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Http\Request;
use App\Http\Response;
use App\Messages\Error;
use App\Services\Database;
use App\Services\Logger;
use App\Services\User as UserService;
use App\Services\Validators\CommandValidator;
use App\Services\Validators\RequestValidator;

final readonly class Handler
{
    /**
     * @param array $input
     */
    public function __construct(
        private array $input
    )
    {}

    /**
     * @return void
     */
    public function handle(): void
    {
        try {
            $requestValidator = new RequestValidator($this->input);
            $requestValidator->validate();
        } catch (InvalidBotActionException $e) {
            $chatId = isset($this->input['edited_message'])
                ? $this->input['edited_message']['chat']['id']
                : $this->input['message']['chat']['id'];

            $response = new Response(
                $chatId,
                $e->getMessage()
            );
            $response->send();
            return;
        }

        $db = Database::getInstance();
        $request = new Request($this->input);
        try {
            $commandValidator = new CommandValidator($request->message);
            $validatedInfo = $commandValidator->validate();
        } catch (InvalidCommandException $e) {
            $response = new Response(
                $request->chatId,
                $e->getMessage()
            );
            $response->send();
            return;
        }

        $userService = new UserService($db);
        try {
            $user = $userService->findByTelegramId($request->userId);
        } catch (UserNotFoundException) {
            $user = $userService->create(
                $request->userId,
                $request->firstName
            );
        }

        try {
            /* @psalm-var class-string $commandClass */
            $commandClass = $validatedInfo['command']->getCommandHandler();
            $command = new $commandClass(
                $user,
                $db,
                $validatedInfo['arguments']
            );
            $message = $command->execute();
        } catch (
            ExpenseNotFoundException
            | InvalidInputException
            | CategoryAliasAlreadyExistsException
            | CategoryAlreadyExistException
            | CategoryAliasNotFoundException
            | CategoryNotFoundException $e
        ) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $logger = new Logger($db);
            $logger->error($e);
            $message = Error::UnknownError->value;
        }

        $response = new Response(
            $request->chatId,
            $message
        );
        $response->send();
    }
}