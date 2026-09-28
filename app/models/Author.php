<?php
namespace App\Models;

use App\Core\Model;

class Author extends Model {
    protected string $table = 'authors';

    public function getAllWithStoryCount(): array {
        $sql = "SELECT a.*, COUNT(s.id) as story_count
                FROM authors a
                LEFT JOIN stories s ON a.id = s.author_id
                GROUP BY a.id
                ORDER BY a.name ASC";
        return $this->db->fetchAll($sql);
    }

    public function findBySlug(string $slug): ?array {
        return $this->firstWhere('slug', $slug);
    }
}
