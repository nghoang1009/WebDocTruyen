<?php
namespace App\Models;

use App\Core\Model;

class Chapter extends Model {
    protected string $table = 'chapters';

    public function getByStoryId(int $storyId, string $order = 'ASC', bool $onlyPublished = true): array {
        $where = "story_id = ?";
        $params = [$storyId];
        if ($onlyPublished) {
            $where .= " AND status = 'published'";
        }
        $sql = "SELECT id, story_id, chapter_number, title, slug, views_count, status, created_at, updated_at
                FROM chapters
                WHERE {$where}
                ORDER BY chapter_number {$order}";
        return $this->db->fetchAll($sql, $params);
    }

    public function getChapterForReading(string $storySlug, string $chapterSlug): ?array {
        $sql = "SELECT c.*, s.title as story_title, s.slug as story_slug, s.cover_image, s.id as story_id
                FROM chapters c
                JOIN stories s ON c.story_id = s.id
                WHERE s.slug = ? AND c.slug = ? AND c.status = 'published'";
        $chapter = $this->db->fetchOne($sql, [$storySlug, $chapterSlug]);
        if (!$chapter) return null;

        // Next chapter
        $nextSql = "SELECT id, chapter_number, title, slug
                    FROM chapters
                    WHERE story_id = ? AND chapter_number > ? AND status = 'published'
                    ORDER BY chapter_number ASC LIMIT 1";
        $chapter['next'] = $this->db->fetchOne($nextSql, [$chapter['story_id'], $chapter['chapter_number']]) ?: null;

        // Prev chapter
        $prevSql = "SELECT id, chapter_number, title, slug
                    FROM chapters
                    WHERE story_id = ? AND chapter_number < ? AND status = 'published'
                    ORDER BY chapter_number DESC LIMIT 1";
        $chapter['prev'] = $this->db->fetchOne($prevSql, [$chapter['story_id'], $chapter['chapter_number']]) ?: null;

        // Chapter list for fast dropdown navigation
        $chapter['all_chapters'] = $this->db->fetchAll(
            "SELECT id, chapter_number, title, slug FROM chapters WHERE story_id = ? AND status = 'published' ORDER BY chapter_number ASC",
            [$chapter['story_id']]
        );

        return $chapter;
    }

    public function getNextChapterNumber(int $storyId): float {
        $max = $this->db->fetchColumn("SELECT MAX(chapter_number) FROM chapters WHERE story_id = ?", [$storyId]);
        return $max ? ((float)$max + 1) : 1.0;
    }
}
