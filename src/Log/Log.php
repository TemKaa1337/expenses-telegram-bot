<?php
declare(strict_types = 1);

namespace App\Log;

use App\Database\Database;
use App\Http\Request;

class Log
{
    protected Database $db;
    protected Request $request;

    public function __construct(Database $db, Request $request) 
    {
        $this->db = $db;
        $this->request = $request;
    }

    public function log() : void
    {
        $this->db->execute('INSERT INTO log (chat_id, request, created_at) INTO log', [$this->request->getChatId(), json_encode($this->request->getInput()), date('Y-m-d H:i:s', strtotime('+3 hours'))]);
    }
}

?>