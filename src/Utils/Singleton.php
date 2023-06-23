<?php declare(strict_types=1);

namespace App\Utils;

use Exception;

class Singleton
{
    /**
     * @var array<static>
     */
    private static array $instances = [];

    protected function __construct() {}

    /**
     * @return static
     */
    public static function getInstance(): static
    {
        $class = static::class;
        self::$instances[$class] ??= new static();
        return self::$instances[$class];
    }

    protected final function __clone() {}

    /**
     * @return void
     * @throws Exception
     */
    public final function __wakeup(): void
    {
        throw new Exception('It is forbidden to unserialize singleton class.');
    }

    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    public final function __unserialize(array $data): void
    {
        throw new Exception('It is forbidden to unserialize singleton class.');
    }
}