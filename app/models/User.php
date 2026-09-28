<?php
namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected string $table = 'users';

    public function findByUsername(string $username): ?array {
        return $this->firstWhere('username', $username);
    }

    public function findByEmail(string $email): ?array {
        return $this->firstWhere('email', $email);
    }

    public function getUserWithRole(int $id): ?array {
        $sql = "SELECT u.id, u.role_id, r.name as role_name, u.username, u.email, u.avatar, u.status, u.created_at, u.last_login
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.id = ?";
        return $this->db->fetchOne($sql, [$id]) ?: null;
    }

    public function getAllUsersWithRoles(int $page = 1, int $limit = 20, string $search = ''): array {
        $offset = ($page - 1) * $limit;
        $params = [];
        $where = "1=1";

        if (!empty($search)) {
            $where .= " AND (u.username LIKE ? OR u.email LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $totalSql = "SELECT COUNT(*) FROM users u WHERE {$where}";
        $total = (int)$this->db->fetchColumn($totalSql, $params);

        $sql = "SELECT u.id, u.role_id, r.name as role_name, u.username, u.email, u.avatar, u.status, u.created_at, u.last_login
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE {$where}
                ORDER BY u.id DESC
                LIMIT {$limit} OFFSET {$offset}";

        $items = $this->db->fetchAll($sql, $params);

        return ['items' => $items, 'total' => $total];
    }
}
