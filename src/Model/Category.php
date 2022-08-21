<?php declare(strict_types = 1);

namespace App\Model;

use App\Database\Database;
use App\Exception\NoSuchCategoryException;
use App\Messages\ErrorMessage;
use App\Model\Checks\CategoryCheck;

class Category
{
    private readonly int $categoryId;

    use CategoryCheck;

    public function __construct(
        private readonly Database $db,
        private readonly int $userId,
        private readonly string $categoryName
    )
    {
        $categoryInfo = $this->db->execute(
            "
                SELECT 
                    id, user_id, category_name 
                FROM 
                    categories 
                WHERE 
                    category_name = ? 
                    AND user_id = ?
            ", 
            [$this->categoryName, $this->userId]
        );

        if (!empty($categoryInfo)) {
            $this->categoryId = $categoryInfo[0]['id'];
        }
    }

    public static function findByAlias(
        Database $db,
        int $userId,
        string $alias
    ): self
    {
        $aliasInfo = $db->execute('SELECT category_id FROM category_aliases WHERE alias = ?', [$alias]);
        if (empty($aliasInfo)) {
            throw new NoSuchCategoryException(ErrorMessage::UnknownCategory->value);
        }

        $categoryInfo = $db->execute('SELECT id, category_name FROM categories WHERE user_id = ? and id = ?', [$userId, $aliasInfo[0]['category_id']]);
        if (empty($categoryInfo)) {
            throw new NoSuchCategoryException(ErrorMessage::UnknownCategory->value);
        }

        return new Category(
            db: $db,
            userId: $userId,
            categoryName: $categoryInfo[0]['category_name']
        );
    }
    
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function add(): void
    {
        $this->checkIfCategoryDoesntExist();
        $query = 'INSERT INTO categories (category_name, user_id) VALUES (?, ?)';
        $this->db->execute($query, [$this->categoryName, $this->userId]);

        $category = new self(db: $this->db, userId: $this->userId, categoryName: $this->categoryName);
        $alias = new CategoryAlias(db: $this->db, category: $category, alias: $this->categoryName);
        $alias->add();
    }

    public function delete(): void
    {
        $this->checkIfCategoryExists();
        $this->db->execute('DELETE FROM categories WHERE id = ?', [$this->categoryId]);
        $this->db->execute('DELETE FROM category_aliases WHERE category_id = ?', [$this->categoryId]);
    }

    public function addAlias(string $alias): void
    {
        $this->checkIfCategoryExists();
        $alias = new CategoryAlias(db: $this->db, category: $this, alias: $alias);
        $alias->add();
    }

    public function getAliases(): array
    {
        $this->checkIfCategoryExists();
        $alias = new CategoryAlias(db: $this->db, category: $this);
        return $alias->getAliases();
    }
}

?>