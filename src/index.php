<?php

namespace App;

include('vendor/autoload.php');

use App\Categories\Categories;
use App\Database\Database;
use App\Command\Command;
use App\Expense\Expense;
use App\Http\Response;
use App\Http\Request;
use App\Model\User;

class App
{
    public function index() : void
    {
        // $db = new Database();
        $request = new Request();
        // $user = new User($request, $db);

        // $category = new Categories($request->getMessage(), $db);
        // $categoryId = $category->getCategoryId();

        // $expense = new Expense($user, $db, $categoryId, $request->getMessage());

        // $command = new Command($request->getMessage(), $expense);
        // $responseMessage = $command->handle();

        $input = file_get_contents('php://input'); 
        $input = json_decode($input, true);
        
        $response = new Response($input['message']['chat']['id']);
        $response->sendResponse('asdasd');
        // $response = new Response($request->getChatId());
        // $response->sendResponse($responseMessage);
    }
}

$app = new App();
$app->index();

?>