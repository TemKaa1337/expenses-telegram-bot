<?php declare(strict_types=1);

namespace App\Models;

final readonly class CategoryAlias
{
    /**
     * @param int $id
     * @param int $categoryId
     * @param string $alias
     */
    public function __construct(
        public int $id,
        public int $categoryId,
        public string $alias
    )
    {}
}