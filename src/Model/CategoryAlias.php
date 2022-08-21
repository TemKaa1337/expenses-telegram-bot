<?php declare(strict_types = 1);

namespace App\Model;

use App\Database\Database;
use App\Exception\NoCategoryAliasesFoundException;
use App\Messages\ErrorMessage;
use App\Model\Checks\CategoryAliasCheck;

class CategoryAlias
{
    private readonly int $aliasId;

    use CategoryAliasCheck;

    public function __construct(
        private readonly Database $db,
        private readonly Category $category,
        private readonly null|string $alias = null
    ) 
    { 
        $this->category->checkIfCategoryExists();
        $categoryAliasInfo = $this->db->execute('SELECT id FROM category_aliases WHERE category_id = ? and alias = ?', [$this->category->getCategoryId(), $this->alias]);
        if (!empty($categoryAliasInfo)) {
            $this->aliasId = $categoryAliasInfo[0]['id'];
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
        $this->category->checkIfCategoryExists();
        $query = 'INSERT INTO category_aliases(category_id, alias) VALUES (?, ?)';
        $this->db->execute($query, [$this->category->getCategoryId(), $this->alias]);
    }

    public function getAliases(): array
    {
        $this->category->checkIfCategoryExists();
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
            throw new NoCategoryAliasesFoundException(ErrorMessage::NoAliasesFound->value);
        }

        return $aliases;
    }
}

?>