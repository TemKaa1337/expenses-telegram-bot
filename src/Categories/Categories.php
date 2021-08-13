<?php

namespace App\Categories;

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
        $aliases = $this->db->execute('SELECT categories.category_name, category_aliases.alias FROM categories JOIN category_aliases ON categories.id = category_aliases.category_id WHERE categories.user_id = '.$userId.' OR user_id is NULL ORDER BY category_aliases.id', []);

        if (empty($aliases)) return 'На данный момент нет ни одной категории!';

        $temp = [];
        $result = [];

        foreach ($aliases as $alias) {
            if (isset($temp[$alias['category_name']]))
                $temp[$alias['category_name']][] = $alias['alias'];
            else
                $temp[$alias['category_name']] = [$alias['alias']];
        }

        foreach ($temp as $name => $aliases) {
            $result[] = "{$name}: ".implode(', ', $aliases).'.';
        }

        return implode(PHP_EOL, $result);
    }
}

?>