<?php declare(strict_types=1);

namespace App\Services\Commands;

use App\Models\User;
use App\Services\Database;

abstract readonly class BaseCommand
{
    /**
     * @param User $user
     * @param Database $db
     * @param array $arguments
     */
    public function __construct(
        protected User $user,
        protected Database $db,
        protected array $arguments
    )
    {}

    /**
     * @return string
     */
    abstract public function execute(): string;
}