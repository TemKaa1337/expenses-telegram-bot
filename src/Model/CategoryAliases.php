<?php declare(strict_types = 1);

namespace App\Model;

use App\Database\Database;
use App\Exception\NoCategoriesFoundException;

class CategoryAliases
{
    public function __construct(
        private readonly Database $db,
        private readonly User $user
    ) {}

    public function getAllALiases(): array
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
                categories.user_id = ?", 
            [$this->user->getDatabaseUserId()]
        );

        if (empty($aliases)) {
            throw new NoCategoriesFoundException('Категорий нет.');
        }
            
        return $aliases;
    }
}