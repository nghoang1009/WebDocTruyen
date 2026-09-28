<?php
namespace App\Core;

abstract class Controller {
    protected Request $request;

    public function __construct() {
        $this->request = new Request();
    }

    protected function json(mixed $data, int $statusCode = 200): void {
        Response::json($data, $statusCode);
    }

    protected function success(mixed $data = null, string $message = 'Thành công', int $statusCode = 200): void {
        Response::success($data, $message, $statusCode);
    }

    protected function error(string $message = 'Đã có lỗi xảy ra', int $statusCode = 400, mixed $errors = null): void {
        Response::error($message, $statusCode, $errors);
    }

    protected function paginate(array $items, int $total, int $page, int $limit, string $message = 'Thành công'): void {
        Response::paginate($items, $total, $page, $limit, $message);
    }

    protected function render(string $view, array $data = []): void {
        extract($data);
        $viewFile = PUBLIC_PATH . "/views/{$view}.php";
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View [{$view}] not found at {$viewFile}");
        }
    }
}
