<?php

namespace App;

include('../vendor/autoload.php');

use App\Exception\{
    InvalidBotActionException,
    InvalidCommandException,
    InvalidInputException,
    InvalidNewAliasException,
    InvalidNewCategoryException,
    NoCategoriesFoundException,
    NoCategoryAliasesFoundException,
    NoExpenseFoundException,
    NoSuchCategoryAliasException,
    NoSuchCategoryException
};
use App\Services\{
    Validator\CommandValidatorService,
    Validator\RequestValidatorService,
    CommandService
};
use App\Messages\ErrorMessage;
use App\Database\SqlDatabase;
use App\Logger\Logger;
use App\Http\Response;
use App\Model\User;

class App
{
    public function handle() : void
    {
        $contents = file_get_contents('php://input');
        $contents = json_decode($contents, true);

        $db = new SqlDatabase();
        $logger = new Logger(db: $db);

        $isUpdate = isset($contents['edited_message']);
        $chatId = $isUpdate
                    ? $contents['edited_message']['chat']['id']
                    : $contents['message']['chat']['id'];

        try {
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
                userId: $user->getDatabaseUserId(), 
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
            |NoExpenseFoundException 
            |NoSuchCategoryException
            |NoSuchCategoryAliasException $e
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