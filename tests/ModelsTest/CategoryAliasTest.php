<?php declare(strict_types=1);

use App\Exception\CategoryAliasAlreadyExistException;
use App\Exception\NoCategoryAliasesFoundException;
use App\Exception\NoSuchCategoryAliasException;
use App\Model\Category;
use App\Model\CategoryAlias;
use PHPUnit\Framework\TestCase;
use App\Database\SqlDatabase;
use App\Model\User;

final class CategoryAliasTest extends TestCase
{
    public function testCategoryAliasDeleteWhenAliasExists(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 10]],
                    []
                ));

        $categoryMock = $this->createMock(Category::class);
        $categoryMock->categoryId = 1;
        $categoryMock->method('getCategoryId')
                    ->willReturn(1);

        $category = new CategoryAlias(db: $dbMock, category: $categoryMock, alias: 'new_alias');
        $empty = $category->delete();
        $this->assertEmpty($empty, 'Function return value is not empty');
    }

    public function testCategoryAliasDeleteWhenAliasDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $categoryMock = $this->createMock(Category::class);
        $categoryMock->categoryId = 1;
        $categoryMock->method('getCategoryId')
                    ->willReturn(1);

        $category = new CategoryAlias(db: $dbMock, category: $categoryMock, alias: 'new_alias');
        $this->expectException(NoSuchCategoryAliasException::class);
        $category->delete();
    }

    public function testCategoryAliasAddWhenAliasExists(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 1]],
                    []
                ));

        $categoryMock = $this->createMock(Category::class);
        $categoryMock->categoryId = 1;
        $categoryMock->method('getCategoryId')
                    ->willReturn(1);

        $category = new CategoryAlias(db: $dbMock, category: $categoryMock, alias: 'new_alias');
        $this->expectException(CategoryAliasAlreadyExistException::class);
        $category->add();
    }

    public function testCategoryAliasAddWhenAliasDoesntExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [],
                    []
                ));

        $categoryMock = $this->createMock(Category::class);
        $categoryMock->categoryId = 1;
        $categoryMock->method('getCategoryId')
                    ->willReturn(1);

        $category = new CategoryAlias(db: $dbMock, category: $categoryMock, alias: 'new_alias');
        $empty = $category->add();
        $this->assertEmpty($empty, 'Function return value is not empty');
    }

    public function testCategoryAliasListWhenAliasesExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [],
                    [
                        ['id'=> 1, 'name' => 'name']
                    ]
                ));

        $categoryMock = $this->createMock(Category::class);
        $categoryMock->categoryId = 1;
        $categoryMock->method('getCategoryId')
                    ->willReturn(1);

        $category = new CategoryAlias(db: $dbMock, category: $categoryMock);
        $aliases = $category->getAliases();
        $this->assertEquals($aliases, [['id'=> 1, 'name' => 'name']], 'Returned alises arent the same with given.');
    }

    public function testCategoryAliasListWhenAliasesDontExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [],
                    []
                ));

        $categoryMock = $this->createMock(Category::class);
        $categoryMock->categoryId = 1;
        $categoryMock->method('getCategoryId')
                    ->willReturn(1);

        $category = new CategoryAlias(db: $dbMock, category: $categoryMock);
        $this->expectException(NoCategoryAliasesFoundException::class);
        $category->getAliases();
    }
}