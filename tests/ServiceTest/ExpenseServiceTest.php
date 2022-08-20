<?php declare(strict_types=1);

use App\Model\User;
use App\Model\Category;
use App\Database\SqlDatabase;
use App\Exception\NoExpenseFoundException;
use App\Services\ExpenseService;
use PHPUnit\Framework\TestCase;

final class ExpenseServiceTest extends TestCase
{
    public function testAddExpenseWithNote(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $categoryMock = $this->createMock(Category::class);
        $categoryMock->categoryId = 1;
        $categoryMock->method('getCategoryId')
                    ->willReturn(1);

        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $result = $expenseService->addExpense(category: $categoryMock, amount: 7.5, note: 'note');
        $this->assertEmpty($result);
    }

    public function testAddExpenseWithoutNote(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $categoryMock = $this->createMock(Category::class);
        $categoryMock->categoryId = 1;
        $categoryMock->method('getCategoryId')
                    ->willReturn(1);

        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $result = $expenseService->addExpense(category: $categoryMock, amount: 7.5, note: '');
        $this->assertEmpty($result);
    }

    public function testDeleteExpenseThatDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $this->expectException(NoExpenseFoundException::class);
        $expenseService->delete(expenseId: 1);
    }

    public function testDeleteExpenseThatExists(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    ['id' => 1],
                    []
                ));

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $empty = $expenseService->delete(expenseId: 1);
        $this->assertEmpty($empty);
    }

    public function testSpecificMonthExpensesWhenExpensesExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([['id' => 1, 'name' => 'name']]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);
        
        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $result = $expenseService->getSpecificMonthExpenses(arrayOfFlags: [], dateFrom: '', dateTo: '');
        $this->assertEquals($result, [['id' => 1, 'name' => 'name']]);
    }

    public function testSpecificMonthExpensesWhenExpensesDontExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $this->expectException(NoExpenseFoundException::class);
        $expenseService->getSpecificMonthExpenses(arrayOfFlags: [], dateFrom: '', dateTo: '');
    }

    public function testSpecificDayExpensesWhenExpensesExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([['id' => 1, 'name' => 'name']]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);
        
        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $result = $expenseService->getSpecificDayExpenses(arrayOfFlags: [], datetimeFrom: '', datetimeTo: '');
        $this->assertEquals($result, [['id' => 1, 'name' => 'name']]);
    }

    public function testSpecificDayExpensesWhenExpensesDontExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $this->expectException(NoExpenseFoundException::class);
        
        $expenseService->getSpecificDayExpenses(arrayOfFlags: [], datetimeFrom: '', datetimeTo: '');
    }

    public function testMonthExpensesByCategoryWhenExpensesExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([['id' => 1, 'name' => 'name']]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);
        
        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $result = $expenseService->getMonthExpensesByCategory(arrayOfFlags: [], datetimeFrom: '', datetimeTo: '');
        $this->assertEquals($result, [['id' => 1, 'name' => 'name']]);
    }

    public function testMonthExpensesByCategoryWhenExpensesDontExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $this->expectException(NoExpenseFoundException::class);
        $expenseService->getMonthExpensesByCategory(arrayOfFlags: [], datetimeFrom: '', datetimeTo: '');
    }

    public function testAverageMonthExpensesWhenExpensesExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([['id' => 1, 'name' => 'name']]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);
        
        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $result = $expenseService->getAverageMonthExpenses(arrayOfFlags: []);
        $this->assertEquals($result, [['id' => 1, 'name' => 'name']]);
    }

    public function testAverageMonthExpensesWhenExpensesDontExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $this->expectException(NoExpenseFoundException::class);
        $expenseService->getAverageMonthExpenses(arrayOfFlags: []);
    }

    public function testTotalMonthExpensesWhenExpensesExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([['id' => 1, 'name' => 'name']]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);
        
        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $result = $expenseService->getTotalMonthExpenses(arrayOfFlags: []);
        $this->assertEquals($result, [['id' => 1, 'name' => 'name']]);
    }

    public function testTotalMonthExpensesWhenExpensesDontExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $this->expectException(NoExpenseFoundException::class);
        $expenseService->getTotalMonthExpenses(arrayOfFlags: []);
    }

    public function testExpensesFromSpecificDatetimeWhenExpensesExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([['id' => 1, 'name' => 'name']]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);
        
        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $result = $expenseService->getExpensesFromSpecificDatetime(arrayOfFlags: [], datetimeFrom: '');
        $this->assertEquals($result, [['id' => 1, 'name' => 'name']]);
    }

    public function testExpensesFromSpecificDatetimeWhenExpensesDontExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $expenseService = new ExpenseService(db: $dbMock, user: $userMock);
        $this->expectException(NoExpenseFoundException::class);
        $expenseService->getExpensesFromSpecificDatetime(arrayOfFlags: [], datetimeFrom: '');
    }
}