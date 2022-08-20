<?php declare(strict_types=1);

use App\Database\SqlDatabase;
use App\Exception\CategoryAlreadyExistException;
use App\Exception\NoSuchCategoryException;
use PHPUnit\Framework\TestCase;
use App\Model\Category;
use App\Model\User;

final class CategoryTest extends TestCase
{
    public function testCategoryAddWhenCategoryDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [],
                    [],
                    ['id' => 10],
                    []
                ));
        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $category = new Category(db: $dbMock, user: $userMock, categoryName: 'new_category_test');
        $empty = $category->add();
        $this->assertEmpty($empty, 'Function return value is not empty');
    }

    public function testCategoryAddWhenCategoryExists(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn(['id' => 10]);
        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $category = new Category(db: $dbMock, user: $userMock, categoryName: 'new_category_test');
        $this->expectException(CategoryAlreadyExistException::class);
        $category->add();
    }

    public function testCategoryDeleteWhenCategoryExists(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    ['id' => 10],
                    [],
                    []
                ));
        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $category = new Category(db: $dbMock, user: $userMock, categoryName: 'new_category_test');
        $empty = $category->delete();
        $this->assertEmpty($empty, 'Function return value is not empty');
    }

    public function testCategoryDeleteWhenCategoryDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);
        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $category = new Category(db: $dbMock, user: $userMock, categoryName: 'new_category_test');
        $this->expectException(NoSuchCategoryException::class);
        $category->delete();
    }

    public function testCategoryAliasAddWhenCategoryDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);
        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $category = new Category(db: $dbMock, user: $userMock, categoryName: 'new_category_test');
        $this->expectException(NoSuchCategoryException::class);
        $category->addAlias(alias: 'alias');
    }

    public function testCategoryAliasListWhenCategoryDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);
        $userMock = $this->createMock(User::class);
        $userMock->method('getDatabaseUserId')
                    ->willReturn(1);

        $category = new Category(db: $dbMock, user: $userMock, categoryName: 'new_category_test');
        $this->expectException(NoSuchCategoryException::class);
        $category->getAliases();
    }
}