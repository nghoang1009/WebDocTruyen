<?php
namespace App\Models;

use App\Core\Model;

class Category extends Model {
    protected string $table = 'categories';

    public function getAllWithStoryCount(): array {
        $sql = "SELECT c.*, COUNT(sc.story_id) as story_count
                FROM categories c
                LEFT JOIN story_categories sc ON c.id = sc.category_id
                GROUP BY c.id
                ORDER BY c.name ASC";
        return $this->db->fetchAll($sql);
    }

    public function findBySlug(string $slug): ?array {
        return $this->firstWhere('slug', $slug);
    }
}
