<?php

namespace App\Categories;

use App\Exception\InvalidNewCategoryException;
use App\Exception\InvalidNewAliasException;
use App\Database\Database;

class Categories
{
    private string $message;
    private Database $db;

    public function __construct(string $message, Database $db)
    {
        $this->message = $message;
        $this->db = $db;
    }

    public function getCategoryId() : int
    {
        if (strpos($this->message, ' ') !== false) {
            $category = explode(' ', $this->message)[1];
            $query = 'SELECT category_id FROM category_aliases where alias = ?';

            $result = $this->db->execute($query, [$category]);

            if (!empty($result)) return $result[0]['category_id'];
        }
        
        return $this->db->execute('SELECT id FROM categories where category_name = ?', ['Другое'])[0]['id'];
    }

    public function getListOfAllAliases(int $userId) : string
    {
        $aliases = $this->db->execute('SELECT categories.id, categories.category_name, categories.user_id, category_aliases.alias FROM categories JOIN category_aliases ON categories.id = category_aliases.category_id WHERE categories.user_id = '.$userId.' OR user_id is NULL ORDER BY category_aliases.id', []);

        if (empty($aliases)) return 'На данный момент нет ни одной категории!';

        $temp = [];
        $result = [];

        foreach ($aliases as $alias) {
            $name = isset($alias['user_id']) ? $alias['category_name'].' (/delete_category'.$alias['id'].')' : $alias['category_name'];

            if (isset($temp[$name]))
                $temp[$name][] = $alias['alias'];
            else
                $temp[$name] = [$alias['alias']];
        }

        foreach ($temp as $name => $aliases) {
            $result[] = "{$name}: ".implode(', ', $aliases).'.';
        }

        return implode(PHP_EOL, $result);
    }

    public function addCategory(int $userId) : string
    {
        if (strpos($this->message, ' ') !== false) {
            $message = explode(' ', $this->message);

            if (count($message) === 2 && !empty($message[1])) {
                $query = 'INSERT INTO categories (category_name, user_id) VALUES (?, ?) RETURNING id';
                $categoryId = $this->db->execute($query, [$message[1], $userId])[0]['id'];
                
                $query = 'INSERT INTO category_aliases(category_id, alias) VALUES (?, ?)';
                $this->db->execute($query, [$categoryId, $message[1]]);
        
                return 'Новая категория успешно добавлена!';
            }
        }

        throw new InvalidNewCategoryException('Неправильный формат добавления категории :(');
    }

    public function addCategoryAlias() : string
    {
        if (strpos($this->message, ' ') !== false) {
            $message = explode(' ', $this->message);

            if (
                count($message) === 3 
                && !empty($message[1]) 
                && !empty($message[2])
            ) {
                $categoryId = $this->getCategoryId();
                $query = 'INSERT INTO category_aliases(category_id, alias) VALUES (?, ?)';
                $this->db->execute($query, [$categoryId, $message[2]]);
        
                return 'Алиас для категории успешно добавлен!';
            }
        }

        throw new InvalidNewAliasException('Неправильный формат добавления алиаса категории :(');
    }

    public function deleteCategory(int $categoryId) : string
    {
        $this->db->execute('DELETE FROM categories WHERE id = ?', [$categoryId]);
        $this->db->execute('DELETE FROM category_aliases WHERE category_id = ?', [$categoryId]);

        return 'Категория успешно удалена!';
    }

    public function isUserAllowedToDeleteCategory(int $userId, int $categoryId) : bool
    {
        $isAllowed = $this->db->execute('SELECT user_id FROM categories WHERE id = ?', [$categoryId]);

        if (empty($isAllowed)) return false;

        return $isAllowed[0]['user_id'] === $userId;
    }
}

?>