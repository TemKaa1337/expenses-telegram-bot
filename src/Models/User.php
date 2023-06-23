<?php declare(strict_types=1);

namespace App\Models;

final readonly class User
{
    /**
     * @param int $id
     * @param int $telegramId
     * @param string $firstName
     */
    public function __construct(
        public int $id,
        public int $telegramId,
        public string $firstName,
    )
    {}
}