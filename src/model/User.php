<?php
declare(strict_types = 1);

namespace App\Model;

class User
{
    private int $requestUserId;

    public function __construct(int $requestUserId)
    {
        $this->requestUserId = $requestUserId;
    }

    public function createOrFind() : void
    {
        
    }
}

?>