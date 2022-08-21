<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Model\User;
use App\Database\SqlDatabase;

final class UserTest extends TestCase
{
    public function testGettingUserId(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([['id' => 16]]);

        $user = new User(db: $dbMock, telegramUserId: 1, firstName: 'Artem');
        $databaseId = $user->getDatabaseUserId();
        $this->assertEquals($databaseId, 16);
    }
}