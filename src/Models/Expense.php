<?php declare(strict_types=1);

namespace App\Models;

final readonly class Expense
{
    /**
     * @param int $id
     * @param int $userId
     * @param int $categoryId
     * @param float $amount
     */
    public function __construct(
        public int $id,
        public int $userId,
        public int $categoryId,
        public float $amount
    )
    {}
}