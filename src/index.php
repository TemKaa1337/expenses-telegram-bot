<?php

namespace App;

include('vendor/autoload.php');

use App\Exception\InvalidCommandException;
use App\Exception\InvalidInputException;
use App\Exception\InvalidNewAliasException;
use App\Exception\InvalidNewCategoryException;
use App\Categories\Categories;
use App\Database\Database;
use App\Command\Command;
use App\Expense\Expense;
use App\Http\Response;
use App\Http\Request;
use App\Model\User;
use App\Log\Log;
use Exception;

class App
{
    public function index() : void
    {
        $db = new Database();
        $request = new Request();

        try {
            $log = new Log($db, $request);
            $log->log();
            
            $user = new User($request, $db);

            $category = new Categories($request->getMessage(), $db);
            $categoryId = $category->getCategoryId();

            $expense = new Expense($user, $db, $categoryId, $request->getMessage());

            $command = new Command($request->getMessage(), $expense, $user);
            $responseMessage = $command->handle();
            
            $response = new Response($request->getChatId());
            $responseInfo = $response->sendResponse($responseMessage);

        } catch (InvalidInputException $e) {
            $response = new Response($request->getChatId());
            $responseInfo = $response->sendResponse($e->getMessage());
        } catch (InvalidCommandException $e) {
            $response = new Response($request->getChatId());
            $responseInfo = $response->sendResponse($e->getMessage());
        } catch (InvalidNewCategoryException $e) {
            $response = new Response($request->getChatId());
            $responseInfo = $response->sendResponse($e->getMessage());
        } catch (InvalidNewAliasException $e) {
            $response = new Response($request->getChatId());
            $responseInfo = $response->sendResponse($e->getMessage());
        } catch (Exception $e) {
            $db->execute('INSERT INTO exception_logging (stack_trace, message, file, line, created_at) VALUES (?, ?, ?, ?, ?)', [$e->getTraceAsString(), $e->getMessage(), $e->getFile(), $e->getLine(), date('Y-m-d H:i:s')]);
            $response = new Response($request->getChatId());
            $responseInfo = $response->sendResponse('Случилась неизвестная ошибка, надо чекать логи((');
        }

        $response->logResponse($db, $responseInfo);
    }
}

$app = new App();
$app->index();

?>