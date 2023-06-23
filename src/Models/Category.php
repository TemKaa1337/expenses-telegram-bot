<?php declare(strict_types=1);

namespace App\Models;

final readonly class Category
{
    /**
     * @param int $id
     * @param string $name
     * @param int $userId
     */
    public function __construct(
        public int $id,
        public string $name,
        public int $userId
    )
    {}
}