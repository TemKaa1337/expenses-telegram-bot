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

        $expense = new Expense(db: $dbMock, userId: 1, expenseId: 1);
        $this->expectException(NoExpenseFoundException::class);
        $expense->delete();
    }

    public function testExpenseDeleteWhenExpenseExists(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn(['id' => 1]);

        $expense = new Expense(db: $dbMock, userId: 1, expenseId: 1);
        $empty = $expense->delete();
        $this->assertEmpty($empty);
    }
}