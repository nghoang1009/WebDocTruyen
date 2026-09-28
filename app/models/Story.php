<?php
namespace App\Models;

use App\Core\Model;

class Story extends Model {
    protected string $table = 'stories';

    public function findBySlug(string $slug): ?array {
        $sql = "SELECT s.*, a.name as author_name, a.slug as author_slug
                FROM stories s
                LEFT JOIN authors a ON s.author_id = a.id
                WHERE s.slug = ?";
        $story = $this->db->fetchOne($sql, [$slug]);
        if (!$story) return null;

        $story['categories'] = $this->getCategories($story['id']);
        $story['latest_chapter'] = $this->getLatestChapter($story['id']);
        $story['chapters_count'] = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM chapters WHERE story_id = ? AND status = 'published'", [$story['id']]);
        $story['first_chapter'] = $this->getFirstChapter($story['id']);
        $story['follows_count'] = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM follows WHERE story_id = ?", [$story['id']]);
        $story['favorites_count'] = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM favorites WHERE story_id = ?", [$story['id']]);

        return $story;
    }

    public function getCategories(int $storyId): array {
        $sql = "SELECT c.id, c.name, c.slug
                FROM categories c
                JOIN story_categories sc ON c.id = sc.category_id
                WHERE sc.story_id = ?";
        return $this->db->fetchAll($sql, [$storyId]);
    }

    public function getLatestChapter(int $storyId): ?array {
        $sql = "SELECT id, chapter_number, title, slug, created_at
                FROM chapters
                WHERE story_id = ? AND status = 'published'
                ORDER BY chapter_number DESC LIMIT 1";
        return $this->db->fetchOne($sql, [$storyId]) ?: null;
    }

    public function getFirstChapter(int $storyId): ?array {
        $sql = "SELECT id, chapter_number, title, slug
                FROM chapters
                WHERE story_id = ? AND status = 'published'
                ORDER BY chapter_number ASC LIMIT 1";
        return $this->db->fetchOne($sql, [$storyId]) ?: null;
    }

    public function listStories(array $filters = [], int $page = 1, int $limit = 12): array {
        $offset = ($page - 1) * $limit;
        $where = ["1=1"];
        $params = [];

        // Category filter
        if (!empty($filters['category'])) {
            $where[] = "s.id IN (SELECT sc.story_id FROM story_categories sc JOIN categories c ON sc.category_id = c.id WHERE c.slug = ? OR c.id = ?)";
            $params[] = $filters['category'];
            $params[] = is_numeric($filters['category']) ? (int)$filters['category'] : 0;
        }

        // Author filter
        if (!empty($filters['author'])) {
            $where[] = "(a.slug = ? OR a.id = ?)";
            $params[] = $filters['author'];
            $params[] = is_numeric($filters['author']) ? (int)$filters['author'] : 0;
        }

        // Status filter
        if (!empty($filters['status']) && in_array($filters['status'], ['updating', 'completed', 'paused'])) {
            $where[] = "s.status = ?";
            $params[] = $filters['status'];
        }

        // Search query
        if (!empty($filters['q'])) {
            $where[] = "(s.title LIKE ? OR a.name LIKE ? OR s.description LIKE ?)";
            $search = "%{$filters['q']}%";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        // Sort options
        $sort = $filters['sort'] ?? 'updated';
        $orderBy = match ($sort) {
            'views'   => 's.views_count DESC',
            'rating'  => 's.rating_avg DESC, s.rating_count DESC',
            'name_az' => 's.title ASC',
            'name_za' => 's.title DESC',
            'newest'  => 's.id DESC',
            default   => 's.updated_at DESC'
        };

        $whereClause = implode(' AND ', $where);

        $totalSql = "SELECT COUNT(DISTINCT s.id)
                     FROM stories s
                     LEFT JOIN authors a ON s.author_id = a.id
                     WHERE {$whereClause}";
        $total = (int)$this->db->fetchColumn($totalSql, $params);

        $sql = "SELECT s.id, s.author_id, s.title, s.slug, s.description, s.cover_image,
                       s.status, s.views_count, s.rating_avg, s.rating_count, s.created_at, s.updated_at,
                       a.name as author_name, a.slug as author_slug
                FROM stories s
                LEFT JOIN authors a ON s.author_id = a.id
                WHERE {$whereClause}
                ORDER BY {$orderBy}
                LIMIT {$limit} OFFSET {$offset}";

        $stories = $this->db->fetchAll($sql, $params);

        foreach ($stories as &$story) {
            $story['categories'] = $this->getCategories($story['id']);
            $story['latest_chapter'] = $this->getLatestChapter($story['id']);
        }

        return [
            'items' => $stories,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit
        ];
    }

    public function syncCategories(int $storyId, array $categoryIds): void {
        $this->db->query("DELETE FROM story_categories WHERE story_id = ?", [$storyId]);
        if (!empty($categoryIds)) {
            $sql = "INSERT INTO story_categories (story_id, category_id) VALUES (?, ?)";
            foreach ($categoryIds as $catId) {
                if ((int)$catId > 0) {
                    $this->db->query($sql, [$storyId, (int)$catId]);
                }
            }
        }
    }

    public function incrementViews(int $storyId, ?int $chapterId = null): void {
        $this->db->query("UPDATE stories SET views_count = views_count + 1 WHERE id = ?", [$storyId]);
        if ($chapterId) {
            $this->db->query("UPDATE chapters SET views_count = views_count + 1 WHERE id = ?", [$chapterId]);
        }
        // Record in views_log for analytics
        $today = date('Y-m-d');
        $sql = "INSERT INTO views_log (story_id, chapter_id, view_date, views_count)
                VALUES (?, ?, ?, 1)
                ON DUPLICATE KEY UPDATE views_count = views_count + 1";
        $this->db->query($sql, [$storyId, $chapterId, $today]);
    }
}
