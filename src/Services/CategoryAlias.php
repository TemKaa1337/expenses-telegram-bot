<?php declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CategoryAliasNotFoundException;
use App\Messages\Error;
use App\Models\CategoryAlias as CategoryAliasModel;
use App\Models\User;

final readonly class CategoryAlias
{
    /**
     * @param Database $db
     * @param User $user
     */
    public function __construct(
        private Database $db,
        private User $user
    )
    {}

    /**
     * @param int $categoryId
     * @param string $name
     * @return CategoryAliasModel
     * @throws CategoryAliasNotFoundException
     */
    public function findByName(int $categoryId, string $name): CategoryAliasModel
    {
        $categoryAliasInfo = $this->db->execute(
            'SELECT * FROM category_alises WHERE category_id = %s AND alias = %s',
            [$categoryId, $name]
        );
        if (empty($categoryAliasInfo)) {
            throw new CategoryAliasNotFoundException(Error::UnknownCategoryAlias->value);
        }

        return new CategoryAliasModel(
            $categoryAliasInfo[0]['id'],
            $categoryAliasInfo[0]['category_id'],
            $categoryAliasInfo[0]['alias'],
        );
    }

    /**
     * @param int $categoryId
     * @param string $alias
     * @return void
     */
    public function create(int $categoryId, string $alias): void
    {
        $this->db->execute(
            'INSERT INTO category_aliases(category_id, alias) VALUES (?, ?)',
            [$categoryId, $alias]
        );
    }

    /**
     * @param CategoryAliasModel $alias
     * @return void
     */
    public function delete(CategoryAliasModel $alias): void
    {
        $this->db->execute(
            'DELETE FROM category_aliases WHERE id = %s',
            [$alias->id]
        );
    }

    /**
     * @param int $categoryId
     * @return void
     */
    public function deleteAll(int $categoryId): void
    {
        $this->db->execute(
            'DELETE FROM category_aliases WHERE category_id = %s',
            [$categoryId]
        );
    }

    /**
     * @param int $categoryId
     * @return array
     */
    public function getAllByCategory(int $categoryId): array
    {
        return $this->db->execute(
            'SELECT * FROM category_aliases WHERE category_id = %s',
            [$categoryId]
        );
    }
}