<?php
namespace App\Models;

use App\Core\Model;

class Rating extends Model {
    protected string $table = 'ratings';

    public function getUserRating(int $userId, int $storyId): ?int {
        $val = $this->db->fetchColumn("SELECT rating FROM ratings WHERE user_id = ? AND story_id = ?", [$userId, $storyId]);
        return $val !== false ? (int)$val : null;
    }

    public function setRating(int $userId, int $storyId, int $rating): array {
        if ($rating < 1 || $rating > 5) {
            throw new \InvalidArgumentException('Rating phải từ 1 đến 5 sao.');
        }

        $sql = "INSERT INTO ratings (user_id, story_id, rating, created_at, updated_at)
                VALUES (?, ?, ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE rating = VALUES(rating), updated_at = NOW()";
        $this->db->query($sql, [$userId, $storyId, $rating]);

        // Recalculate average and count on stories table
        $stats = $this->db->fetchOne(
            "SELECT COUNT(*) as cnt, AVG(rating) as avg_rating FROM ratings WHERE story_id = ?",
            [$storyId]
        );

        $cnt = (int)($stats['cnt'] ?? 0);
        $avg = round((float)($stats['avg_rating'] ?? 0), 2);

        $this->db->query("UPDATE stories SET rating_avg = ?, rating_count = ? WHERE id = ?", [$avg, $cnt, $storyId]);

        return [
            'rating_avg'   => $avg,
            'rating_count' => $cnt,
            'user_rating'  => $rating
        ];
    }
}
