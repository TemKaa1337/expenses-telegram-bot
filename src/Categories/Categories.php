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

            if (!empty($result)) return $result['category_id'];
        }
        
        return $this->db->execute('SELECT id FROM categories where category_name = ?', ['Другое'])['id'];
    }
}

?>