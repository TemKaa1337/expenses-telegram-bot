<?php

namespace App;

use App\Http\Request;
use App\Http\Response;
use App\Database\Database;

class App
{
    public function index() : void
    {
        $request = new Request();
        $response = new Response($request);

        $response->handleRequest();
    }
}

$app = new App();
$app->index();

?>