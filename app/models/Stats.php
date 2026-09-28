<?php
namespace App\Models;

use App\Core\Model;

class Stats extends Model {
    public function getOverview(): array {
        $totalStories = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM stories");
        $totalChapters = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM chapters");
        $totalUsers = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM users");
        $totalViews = (int)$this->db->fetchColumn("SELECT SUM(views_count) FROM stories");
        $totalComments = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM comments");

        return [
            'total_stories'  => $totalStories,
            'total_chapters' => $totalChapters,
            'total_users'    => $totalUsers,
            'total_views'    => $totalViews,
            'total_comments' => $totalComments
        ];
    }

    public function getDailyViews(int $days = 7): array {
        $sql = "SELECT view_date, SUM(views_count) as total_views
                FROM views_log
                WHERE view_date >= CURDATE() - INTERVAL ? DAY
                GROUP BY view_date
                ORDER BY view_date ASC";
        return $this->db->fetchAll($sql, [$days]);
    }

    public function getDailyNewUsers(int $days = 7): array {
        $sql = "SELECT DATE(created_at) as register_date, COUNT(*) as new_users
                FROM users
                WHERE created_at >= CURDATE() - INTERVAL ? DAY
                GROUP BY DATE(created_at)
                ORDER BY register_date ASC";
        return $this->db->fetchAll($sql, [$days]);
    }

    public function getTopStories(int $limit = 5): array {
        $sql = "SELECT s.id, s.title, s.slug, s.cover_image, s.views_count, s.rating_avg, s.status, a.name as author_name
                FROM stories s
                LEFT JOIN authors a ON s.author_id = a.id
                ORDER BY s.views_count DESC
                LIMIT {$limit}";
        return $this->db->fetchAll($sql);
    }

    public function getTopChapters(int $limit = 5): array {
        $sql = "SELECT c.id, c.title, c.chapter_number, c.views_count, s.title as story_title, s.slug as story_slug
                FROM chapters c
                JOIN stories s ON c.story_id = s.id
                ORDER BY c.views_count DESC
                LIMIT {$limit}";
        return $this->db->fetchAll($sql);
    }
}
