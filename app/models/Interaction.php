<?php
namespace App\Models;

use App\Core\Model;

class Interaction extends Model {
    // --- READING HISTORY ---
    public function saveHistory(int $userId, int $storyId, int $chapterId, int $progress = 0): void {
        $sql = "INSERT INTO reading_history (user_id, story_id, chapter_id, progress_percent, last_read_at)
                VALUES (?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), progress_percent = VALUES(progress_percent), last_read_at = NOW()";
        $this->db->query($sql, [$userId, $storyId, $chapterId, $progress]);
    }

    public function getHistory(int $userId, int $page = 1, int $limit = 20): array {
        $offset = ($page - 1) * $limit;
        $total = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM reading_history WHERE user_id = ?", [$userId]);

        $sql = "SELECT rh.*, s.title as story_title, s.slug as story_slug, s.cover_image, s.status as story_status,
                       c.title as chapter_title, c.chapter_number, c.slug as chapter_slug
                FROM reading_history rh
                JOIN stories s ON rh.story_id = s.id
                JOIN chapters c ON rh.chapter_id = c.id
                WHERE rh.user_id = ?
                ORDER BY rh.last_read_at DESC
                LIMIT {$limit} OFFSET {$offset}";

        $items = $this->db->fetchAll($sql, [$userId]);
        return ['items' => $items, 'total' => $total, 'page' => $page, 'limit' => $limit];
    }

    public function deleteHistoryItem(int $userId, int $storyId): bool {
        $stmt = $this->db->query("DELETE FROM reading_history WHERE user_id = ? AND story_id = ?", [$userId, $storyId]);
        return $stmt->rowCount() > 0;
    }

    public function clearHistory(int $userId): bool {
        $stmt = $this->db->query("DELETE FROM reading_history WHERE user_id = ?", [$userId]);
        return $stmt->rowCount() > 0;
    }

    // --- FOLLOWS ---
    public function toggleFollow(int $userId, int $storyId): bool {
        $exists = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM follows WHERE user_id = ? AND story_id = ?", [$userId, $storyId]);
        if ($exists > 0) {
            $this->db->query("DELETE FROM follows WHERE user_id = ? AND story_id = ?", [$userId, $storyId]);
            return false; // unfollowed
        } else {
            $this->db->query("INSERT INTO follows (user_id, story_id, created_at) VALUES (?, ?, NOW())", [$userId, $storyId]);
            return true; // followed
        }
    }

    public function isFollowing(int $userId, int $storyId): bool {
        return (int)$this->db->fetchColumn("SELECT COUNT(*) FROM follows WHERE user_id = ? AND story_id = ?", [$userId, $storyId]) > 0;
    }

    public function getFollowing(int $userId, int $page = 1, int $limit = 12): array {
        $offset = ($page - 1) * $limit;
        $total = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM follows WHERE user_id = ?", [$userId]);

        $sql = "SELECT f.created_at as followed_at, s.id, s.title, s.slug, s.cover_image, s.status, s.views_count, s.rating_avg, s.updated_at,
                       a.name as author_name
                FROM follows f
                JOIN stories s ON f.story_id = s.id
                LEFT JOIN authors a ON s.author_id = a.id
                WHERE f.user_id = ?
                ORDER BY s.updated_at DESC
                LIMIT {$limit} OFFSET {$offset}";

        $items = $this->db->fetchAll($sql, [$userId]);

        $storyModel = new Story();
        foreach ($items as &$item) {
            $item['latest_chapter'] = $storyModel->getLatestChapter($item['id']);
            $item['categories'] = $storyModel->getCategories($item['id']);
        }

        return ['items' => $items, 'total' => $total, 'page' => $page, 'limit' => $limit];
    }

    // --- FAVORITES ---
    public function toggleFavorite(int $userId, int $storyId): bool {
        $exists = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM favorites WHERE user_id = ? AND story_id = ?", [$userId, $storyId]);
        if ($exists > 0) {
            $this->db->query("DELETE FROM favorites WHERE user_id = ? AND story_id = ?", [$userId, $storyId]);
            return false; // unfavorited
        } else {
            $this->db->query("INSERT INTO favorites (user_id, story_id, created_at) VALUES (?, ?, NOW())", [$userId, $storyId]);
            return true; // favorited
        }
    }

    public function isFavorited(int $userId, int $storyId): bool {
        return (int)$this->db->fetchColumn("SELECT COUNT(*) FROM favorites WHERE user_id = ? AND story_id = ?", [$userId, $storyId]) > 0;
    }
}
