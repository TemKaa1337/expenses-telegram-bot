<?php declare(strict_types=1);

require './vendor/autoload.php';

use App\Handler;

$input = file_get_contents('php://input');
$handler = new Handler(json_decode($input, true));
$handler->handle();