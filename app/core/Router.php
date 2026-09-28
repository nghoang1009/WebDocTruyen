<?php
namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, array|callable|string $handler): void {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array|callable|string $handler): void {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, array|callable|string $handler): void {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, array|callable|string $handler): void {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function any(string $path, array|callable|string $handler): void {
        $this->addRoute('ANY', $path, $handler);
    }

    private function addRoute(string $method, string $path, array|callable|string $handler): void {
        // Convert route pattern like /stories/:slug to regex
        $pattern = preg_replace('/\/:([^\/]+)/', '/(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'  => $method,
            'path'    => $path,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    public function dispatch(): void {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // 1. Strip APP_URL base path (e.g. /WebDocTruyen) - Case-insensitive for Windows Apache
        if (defined('APP_URL')) {
            $appPath = parse_url(APP_URL, PHP_URL_PATH);
            if (!empty($appPath) && $appPath !== '/' && stripos($uri, $appPath) === 0) {
                $uri = substr($uri, strlen($appPath));
            }
        }

        // 2. Strip /public prefix if present
        if (stripos($uri, '/public') === 0) {
            $uri = substr($uri, 7);
        }

        // 3. Normalize URI
        $uri = '/' . trim($uri, '/');
        if (empty($uri) || $uri === '//') {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== 'ANY' && $route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = urldecode($value);
                    }
                }

                $handler = $route['handler'];

                if (is_callable($handler)) {
                    call_user_func_array($handler, $params);
                    return;
                }

                if (is_array($handler)) {
                    [$controllerClass, $action] = $handler;
                    $controller = new $controllerClass();
                    call_user_func_array([$controller, $action], $params);
                    return;
                }

                if (is_string($handler)) {
                    // Extract route parameters ($slug, $storySlug, $chapterSlug, etc.) into view scope
                    extract($params);
                    $viewFile = PUBLIC_PATH . "/views/{$handler}.php";
                    if (file_exists($viewFile)) {
                        require_once $viewFile;
                        return;
                    }
                }
            }
        }

        // Check if API or Web 404
        if (str_starts_with($uri, '/api/')) {
            Response::error('Endpoint API không tồn tại.', 404);
        } else {
            http_response_code(404);
            $notFoundView = PUBLIC_PATH . "/views/404.php";
            if (file_exists($notFoundView)) {
                require_once $notFoundView;
            } else {
                echo "<div style='text-align:center; padding:50px; font-family:sans-serif;'><h2>404 - Không tìm thấy trang</h2><a href='" . APP_URL . "'>Về trang chủ</a></div>";
            }
        }
    }
}
