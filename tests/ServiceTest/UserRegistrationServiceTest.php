<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Database\SqlDatabase;
use App\Services\UserRegistrationService;

final class UserRegistrationServiceTest extends TestCase
{
    public function testRegisterUserThatAlreadyExists(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    ['id' => 10],
                    []
                ));

        $userRegistrationService = new UserRegistrationService(db: $dbMock, telegramUserId: 1, firstName: 'artem');
        $result = $userRegistrationService->registrateIfUnregistered();
        $this->assertEmpty($result);
    }

    public function testRegisterUserThatDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $userRegistrationService = new UserRegistrationService(db: $dbMock, telegramUserId: 1, firstName: 'artem');
        $result = $userRegistrationService->registrateIfUnregistered();
        $this->assertEmpty($result);
    }
}