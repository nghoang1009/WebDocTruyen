<?php
namespace App\Core;

class Request {
    private array $body = [];
    private array $query = [];
    private array $params = [];

    public function __construct() {
        $this->query = $_GET;
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'application/json')) {
            $input = file_get_contents('php://input');
            $this->body = json_decode($input, true) ?? [];
        } else {
            $this->body = $_POST;
        }
    }

    public function getMethod(): string {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function get(string $key, mixed $default = null): mixed {
        return $this->query[$key] ?? $this->body[$key] ?? $default;
    }

    public function query(string $key = null, mixed $default = null): mixed {
        if ($key === null) return $this->query;
        return $this->query[$key] ?? $default;
    }

    public function input(string $key = null, mixed $default = null): mixed {
        if ($key === null) return $this->body;
        return $this->body[$key] ?? $default;
    }

    public function all(): array {
        return array_merge($this->query, $this->body, $this->params);
    }

    public function setParams(array $params): void {
        $this->params = $params;
    }

    public function param(string $key, mixed $default = null): mixed {
        return $this->params[$key] ?? $default;
    }

    public function getBearerToken(): ?string {
        $headers = $this->getHeaders();
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }
        return $_COOKIE['auth_token'] ?? null;
    }

    public function getHeaders(): array {
        if (function_exists('getallheaders')) {
            return getallheaders() ?: [];
        }
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (str_starts_with($name, 'HTTP_')) {
                $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

    public function sanitize(mixed $data): mixed {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->sanitize($v);
            }
            return $data;
        }
        if (is_string($data)) {
            return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
        }
        return $data;
    }
}
