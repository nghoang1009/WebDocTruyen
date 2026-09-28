<?php
namespace App\Core;

class Response {
    public static function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public static function success(mixed $data = null, string $message = 'Thành công', int $statusCode = 200): void {
        self::json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $statusCode);
    }

    public static function error(string $message = 'Đã có lỗi xảy ra', int $statusCode = 400, mixed $errors = null): void {
        self::json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors
        ], $statusCode);
    }

    public static function paginate(array $items, int $total, int $page, int $limit, string $message = 'Thành công'): void {
        $totalPages = ceil($total / ($limit > 0 ? $limit : 10));
        self::json([
            'success' => true,
            'message' => $message,
            'data'    => $items,
            'pagination' => [
                'total'        => $total,
                'page'         => $page,
                'limit'        => $limit,
                'total_pages'  => (int)$totalPages,
                'has_prev'     => $page > 1,
                'has_next'     => $page < $totalPages
            ]
        ]);
    }
}
