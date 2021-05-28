<?php

namespace App\Categories;

class Categories
{
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getCategory() : string
    {
        //TODO get category by message
        return $this->message;
    }
}

?>