<?php declare(strict_types = 1);

namespace App\Model;

use App\Exception\CategoryAlreadyExistException;
use App\Exception\NoSuchCategoryException;
use App\Database\Database;
use App\Messages\ErrorMessage;
use App\Model\User;

class Category
{
    private readonly int $categoryId;

    public function __construct(
        private readonly Database $db,
        private readonly User $user,
        private readonly string $categoryName
    )
    {
        $this->setCategoryInfo();
    }

    private function setCategoryInfo(): void
    {
        $categoryInfo = $this->db->execute(
            // "
            //     SELECT 
            //         categories.id, user_id, alias 
            //     FROM 
            //         categories 
            //     JOIN 
            //         category_aliases 
            //     ON 
            //         categories.id = category_aliases.category_id 
            //     WHERE 
            //         alias = ? 
            //         AND user_id = ?
            // ", 
            "
            SELECT 
                id, user_id, category_name 
            FROM 
                categories 
            WHERE 
                category_name = ? 
                AND user_id = ?
        ", 
            [$this->categoryName, $this->user->getDatabaseUserId()]
        );

        if (!empty($categoryInfo)) {
            $this->categoryId = $categoryInfo[0]['id'];
        }
    }

    public function checkIfCategoryExists(): void
    {
        if (!isset($this->categoryId)) {
            throw new NoSuchCategoryException(ErrorMessage::UnknownCategory->value);
        }
    }

    private function checkIfCategoryDoesntExist(): void
    {
        if (isset($this->categoryId)) {
            throw new CategoryAlreadyExistException(ErrorMessage::CategoryAlreadyExist->value);
        }
    }
    
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function add(): void
    {
        $this->checkIfCategoryDoesntExist();
        $query = 'INSERT INTO categories (category_name, user_id) VALUES (?, ?)';
        $this->db->execute($query, [$this->categoryName, $this->user->getDatabaseUserId()]);
        $this->setCategoryInfo();

        $alias = new CategoryAlias(db: $this->db, category: $this, alias: $this->categoryName);
        $alias->add();
        
        // $query = 'INSERT INTO category_aliases(category_id, alias) VALUES (?, ?)';
        // $this->db->execute($query, [$this->categoryId, $this->categoryName]);
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