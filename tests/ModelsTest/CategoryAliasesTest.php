<?php declare(strict_types=1);

use App\Model\CategoryAliases;
use PHPUnit\Framework\TestCase;
use App\Model\User;
use App\Database\SqlDatabase;
use App\Exception\NoCategoriesFoundException;

final class CategoryAliasesTest extends TestCase
{
    public function testCategoryAliasesDontExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->willReturn([]);

        $aliases = new CategoryAliases(db: $dbMock, userId: 1);
        $this->expectException(NoCategoriesFoundException::class);
        $aliases->getAllALiases();
    }

    public function testCategoryAliasesExist(): void
    {
        $dbMock = $this->createMock(SqlDatabase::class);
        $dbMock->method('execute')
                ->will($this->onConsecutiveCalls(
                    [['id' => 10, 'name' => 'name']]
                ));

        $aliases = new CategoryAliases(db: $dbMock, userId: 1);
        $result = $aliases->getAllALiases();
        $this->assertEquals($result, [['id' => 10, 'name' => 'name']]);
    }
}