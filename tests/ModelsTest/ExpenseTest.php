<?php declare(strict_types=1);

use App\Exception\NoExpenseFoundException;
use App\Model\Expense;
use PHPUnit\Framework\TestCase;
use App\Database\SqlDatabase;
use App\Model\User;

final class ExpenseTest extends TestCase
{
    public function testExpenseDeleteWhenExpenseDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $expense = new Expense(db: $dbMock, user: $userMock, expenseId: 1);
        $this->expectException(NoExpenseFoundException::class);
        $expense->delete();
    }

    public function testExpenseDeleteWhenExpenseExists(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn(['id' => 1]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $expense = new Expense(db: $dbMock, user: $userMock, expenseId: 1);
        $empty = $expense->delete();
        $this->assertEmpty($empty);
    }
}