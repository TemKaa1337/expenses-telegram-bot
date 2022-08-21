<?php declare(strict_types = 1);

namespace App\Database;

interface Database
{
    public function execute(string $query, array $bindings): array;
}