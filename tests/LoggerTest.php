<?php declare(strict_types=1);

use App\Database\SqlDatabase;
use App\Logger\Logger;
use PHPUnit\Framework\TestCase;

final class LoggerTest extends TestCase
{
    public function testLoggingInfoMessageWillNotThrowException(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $logger = new Logger(db: $dbMock);
        $empty = $logger->info(chatId: 12345, type: 'info', message: ['test' => 'test']);
        $this->assertEmpty($empty, 'Function return value is not empty');
    }

    
    public function testLoggingErrorMessageWillNotThrowException(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $logger = new Logger(db: $dbMock);
        $empty = $logger->error(new \Exception());
        $this->assertEmpty($empty, 'Function return value is not empty');
    }
}