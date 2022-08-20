<?php declare(strict_types = 1);

namespace App\Model;

use App\Database\Database;
use App\Exception\CategoryAliasAlreadyExistException;
use App\Exception\NoCategoryAliasesFoundException;
use App\Exception\NoSuchCategoryAliasException;

class CategoryAlias
{
    private readonly int $aliasId;

    public function __construct(
        private readonly Database $db,
        private readonly Category $category,
        private readonly null|string $alias = null
    ) 
    { 
        $this->setCategoryAliasInfo();
    }

    private function setCategoryAliasInfo(): void
    {
        $categoryAliasInfo = $this->db->execute('SELECT id FROM category_aliases WHERE category_id = ? and alias = ?', [$this->category->getCategoryId(), $this->alias]);
        if (isset($categoryAliasInfo['id'])) {
            $this->aliasId = $categoryAliasInfo['id'];
        }
    }

    private function checkIfCategoryAliasExists(): void
    {
        if (!isset($this->aliasId)) {
            throw new NoSuchCategoryAliasException('Такого алиаса категории не существует.');
        }
    }

    private function checkIfCategoryAliasDoesntExist(): void
    {
        if (isset($this->aliasId)) {
            throw new CategoryAliasAlreadyExistException('Такой алиас категории уже существует');
        }
    }

    public function delete(): void
    {
        $this->checkIfCategoryAliasExists();
        $this->db->execute('DELETE FROM category_aliases WHERE id = ?', [$this->aliasId]);
    }

    public function add(): void
    {
        $this->checkIfCategoryAliasDoesntExist();
        $query = 'INSERT INTO category_aliases(category_id, alias) VALUES (?, ?)';
        $this->db->execute($query, [$this->category->getCategoryId(), $this->alias]);
    }

    public function getAliases(): array
    {
        $aliases = $this->db->execute("
            SELECT 
                categories.id, 
                categories.category_name, 
                categories.user_id, 
                category_aliases.alias 
            FROM 
                categories 
            JOIN 
                category_aliases 
            ON 
                categories.id = category_aliases.category_id 
            WHERE 
                categories.id = ?", 
            [$this->category->getCategoryId()]
        );

        if (empty($aliases)) {
            throw new NoCategoryAliasesFoundException('Для выбранной категории нет алиасов.');
        }

        return $aliases;
    }
}

?>