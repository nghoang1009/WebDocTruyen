<?php
namespace App\Models;

use App\Core\Model;

class Comment extends Model {
    protected string $table = 'comments';

    public function getByStory(int $storyId, int $page = 1, int $limit = 20): array {
        $offset = ($page - 1) * $limit;

        $totalSql = "SELECT COUNT(*) FROM comments WHERE story_id = ? AND parent_id IS NULL";
        $total = (int)$this->db->fetchColumn($totalSql, [$storyId]);

        // Get top-level comments
        $sql = "SELECT c.*, u.username, u.avatar, u.role_id, r.name as role_name, ch.title as chapter_title, ch.chapter_number
                FROM comments c
                JOIN users u ON c.user_id = u.id
                JOIN roles r ON u.role_id = r.id
                LEFT JOIN chapters ch ON c.chapter_id = ch.id
                WHERE c.story_id = ? AND c.parent_id IS NULL
                ORDER BY c.id DESC
                LIMIT {$limit} OFFSET {$offset}";
        $comments = $this->db->fetchAll($sql, [$storyId]);

        // Fetch replies for these comments
        foreach ($comments as &$comment) {
            $replySql = "SELECT c.*, u.username, u.avatar, u.role_id, r.name as role_name
                         FROM comments c
                         JOIN users u ON c.user_id = u.id
                         JOIN roles r ON u.role_id = r.id
                         WHERE c.parent_id = ?
                         ORDER BY c.id ASC";
            $comment['replies'] = $this->db->fetchAll($replySql, [$comment['id']]);
        }

        return [
            'items' => $comments,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit
        ];
    }

    public function getAllAdmin(int $page = 1, int $limit = 20, string $search = ''): array {
        $offset = ($page - 1) * $limit;
        $where = "1=1";
        $params = [];

        if (!empty($search)) {
            $where .= " AND (c.content LIKE ? OR u.username LIKE ? OR s.title LIKE ?)";
            $params = ["%{$search}%", "%{$search}%", "%{$search}%"];
        }

        $totalSql = "SELECT COUNT(*) FROM comments c JOIN users u ON c.user_id = u.id JOIN stories s ON c.story_id = s.id WHERE {$where}";
        $total = (int)$this->db->fetchColumn($totalSql, $params);

        $sql = "SELECT c.*, u.username, u.email, s.title as story_title, s.slug as story_slug
                FROM comments c
                JOIN users u ON c.user_id = u.id
                JOIN stories s ON c.story_id = s.id
                WHERE {$where}
                ORDER BY c.id DESC
                LIMIT {$limit} OFFSET {$offset}";
        $items = $this->db->fetchAll($sql, $params);

        return ['items' => $items, 'total' => $total];
    }
}
