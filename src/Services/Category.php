<?php declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CategoryNotFoundException;
use App\Messages\Error;
use App\Models\Category as CategoryModel;
use App\Models\User;

final readonly class Category
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
     * @param string $alias
     * @return CategoryModel
     * @throws CategoryNotFoundException
     */
    public function findByAlias(string $alias): CategoryModel
    {
        $categoryAliasInfo = $this->db->execute(
            'SELECT category_id FROM category_alises WHERE alias = %s',
            [$alias]
        );
        if (empty($categoryAliasInfo)) {
            throw new CategoryNotFoundException(Error::UnknownCategory->value);
        }

        $categoryInfo = $this->db->execute(
            'SELECT * FROM categories WHERE id = %s AND user_id = %s',
            [$categoryAliasInfo[0]['category_id'], $this->user->id]
        );

        if (empty($categoryInfo)) {
            throw new CategoryNotFoundException(Error::UnknownCategory->value);
        }

        return new CategoryModel(
            $categoryInfo[0]['id'],
            $categoryInfo[0]['category_name'],
            $categoryInfo[0]['user_id']
        );
    }

    /**
     * @param string $name
     * @return CategoryModel
     * @throws CategoryNotFoundException
     */
    public function findByName(string $name): CategoryModel
    {
        $categoryInfo = $this->db->execute(
            'SELECT * FROM categories WHERE category_name = %s AND user_id = %s',
            [$name, $this->user->id]
        );

        if (empty($categoryInfo)) {
            throw new CategoryNotFoundException(Error::UnknownCategory->value);
        }

        return new CategoryModel(
            $categoryInfo[0]['id'],
            $categoryInfo[0]['category_name'],
            $categoryInfo[0]['user_id']
        );
    }

    /**
     * @param string $name
     * @return CategoryModel
     */
    public function create(
        string $name
    ): CategoryModel
    {
        $categoryInfo = $this->db->execute(
            'INSERT INTO categories(category_name, user_id) VALUES (%s, %s) RETURNING id',
            [$name, $this->user->id]
        );
        return new CategoryModel(
            $categoryInfo[0]['id'],
            $name,
            $this->user->id
        );
    }

    /**
     * @return array
     * @throws CategoryNotFoundException
     */
    public function getAll(): array
    {
        $categoriesInfo = $this->db->execute(
            'SELECT * FROM categories WHERE user_id = %s',
            [$this->user->id]
        );
        if (empty($categoriesInfo)) {
            throw new CategoryNotFoundException(Error::UnknownCategory->value);
        }

        $categories = [];
        foreach ($categoriesInfo as $category) {
            $categories[] = new CategoryModel(
                $category['id'],
                $category['category_name'],
                $category['user_id']
            );
        }
        return $categories;
    }

    /**
     * @param CategoryModel $category
     * @return void
     */
    public function delete(CategoryModel $category): void
    {
        $this->db->execute(
            'DELETE FROM categories WHERE id = %s AND user_id = %s',
            [$category->id, $this->user->id]
        );
    }
}