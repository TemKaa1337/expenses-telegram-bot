<?php
declare(strict_types = 1);

namespace App\Helper;

class Helper
{
    private string $s;

    public static function str(string $s) : self
    {
        $self = new self();
        $self->s = $s;

        return $self;
    }

    public function startsWith(string $start) : bool
    {
        return substr($this->s, 0, strlen($start)) == $start;
    }
}

?>