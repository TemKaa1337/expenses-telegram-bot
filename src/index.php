<?php

namespace App;

include('../vendor/autoload.php');

use App\Database\SqlDatabase;
use App\Exception\InvalidBotActionException;
use App\Exception\InvalidCommandException;
use App\Exception\InvalidInputException;
use App\Exception\InvalidNewAliasException;
use App\Exception\InvalidNewCategoryException;
use App\Exception\NoCategoriesFoundException;
use App\Exception\NoCategoryAliasesFoundException;
use App\Exception\NoExpenseFoundException;
use App\Exception\UpdateNotAllowedException;
use App\Http\Response;
use App\Model\User;
use App\Logger\Logger;
use App\Messages\ErrorMessage;
use App\Services\Validator\CommandValidatorService;
use App\Services\Validator\RequestValidatorService;
use App\Services\CommandService;

class App
{
    public function handle() : void
    {
        $contents = file_get_contents('php://input');
        $contents = json_decode($contents, true);

        $db = new SqlDatabase();
        $logger = new Logger(db: $db);

        $logger->info(chatId: 123, type: 'request', message: $contents);

        $isUpdate = isset($contents['edited_message']);
        $chatId = $isUpdate
                    ? $contents['edited_message']['chat']['id']
                    : $contents['message']['chat']['id'];

        try {
            if ($isUpdate) {
                throw new UpdateNotAllowedException(ErrorMessage::UpdateNotAllowed->value);
            }

            $logger->info(chatId: $chatId, type: 'request', message: $contents);

            $requestValidator = new RequestValidatorService(input: $contents);
            $contents = $requestValidator->validate();
            $commandValidator = new CommandValidatorService(command: $contents['message']['text']);
            $commandInfo = $commandValidator->validate();

            $user = new User(
                db: $db,
                telegramUserId: $contents['message']['from']['id'], 
                firstName: $contents['message']['from']['first_name']
            );

            $command = new CommandService(
                db: $db,
                user: $user, 
                command: $commandInfo['command'], 
                arguments: $commandInfo['arguments']
            );
            $message = $command->handle();
        } catch (
            InvalidInputException
            |InvalidCommandException
            |InvalidNewCategoryException
            |InvalidNewAliasException
            |InvalidBotActionException
            |NoCategoriesFoundException
            |NoCategoryAliasesFoundException
            |NoExpenseFoundException $e
        ) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $logger->error(error: $e);
            $message = ErrorMessage::UnknownError->value;
        }

        $response = new Response(chatId: $chatId, message: $message);
        $responseOutput = $response->sendResponse();

        $logger->info(chatId: $chatId, type: 'response', message: $responseOutput);
    }
}

$app = new App();
$app->handle();

?>