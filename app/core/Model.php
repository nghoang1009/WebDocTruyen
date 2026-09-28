<?php
namespace App\Core;

abstract class Model {
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array {
        $row = $this->db->fetchOne("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?", [$id]);
        return $row ?: null;
    }

    public function all(string $orderBy = 'id DESC'): array {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
    }

    public function where(string $column, mixed $value, string $operator = '='): array {
        return $this->db->fetchAll("SELECT * FROM {$this->table} WHERE {$column} {$operator} ?", [$value]);
    }

    public function firstWhere(string $column, mixed $value, string $operator = '='): ?array {
        $row = $this->db->fetchOne("SELECT * FROM {$this->table} WHERE {$column} {$operator} ? LIMIT 1", [$value]);
        return $row ?: null;
    }

    public function count(string $where = '1=1', array $params = []): int {
        return (int)$this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table} WHERE {$where}", $params);
    }

    public function create(array $data): int {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $this->db->query($sql, array_values($data));
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = array_keys($data);
        $setClauses = array_map(fn($f) => "{$f} = ?", $fields);
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE {$this->primaryKey} = ?";
        $params = array_values($data);
        $params[] = $id;
        $stmt = $this->db->query($sql, $params);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool {
        $stmt = $this->db->query("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?", [$id]);
        return $stmt->rowCount() > 0;
    }
}
