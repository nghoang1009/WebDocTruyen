<?php
namespace App\Core;

class Auth {
    private static ?array $currentUser = null;

    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    public static function generateToken(array $payload, int $expiry = SESSION_LIFETIME): string {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload['exp'] = time() + $expiry;
        $payload['iat'] = time();
        $payloadJson = json_encode($payload);

        $base64Header = self::base64UrlEncode($header);
        $base64Payload = self::base64UrlEncode($payloadJson);

        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, APP_KEY, true);
        $base64Signature = self::base64UrlEncode($signature);

        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }

    public static function verifyToken(string $token): ?array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$base64Header, $base64Payload, $base64Signature] = $parts;

        $signature = self::base64UrlDecode($base64Signature);
        $expectedSignature = hash_hmac('sha256', $base64Header . "." . $base64Payload, APP_KEY, true);

        if (!hash_equals($signature, $expectedSignature)) {
            return null;
        }

        $payload = json_decode(self::base64UrlDecode($base64Payload), true);
        if (!$payload || !isset($payload['exp']) || $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    public static function user(): ?array {
        if (self::$currentUser !== null) {
            return self::$currentUser;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check Session first
        if (isset($_SESSION['user_id'])) {
            $db = Database::getInstance();
            $user = $db->fetchOne(
                "SELECT u.id, u.role_id, r.name as role_name, u.username, u.email, u.avatar, u.status, u.created_at
                 FROM users u
                 JOIN roles r ON u.role_id = r.id
                 WHERE u.id = ? AND u.status = 'active'",
                [$_SESSION['user_id']]
            );
            if ($user) {
                self::$currentUser = $user;
                return self::$currentUser;
            }
        }

        // Check Bearer Token
        $request = new Request();
        $token = $request->getBearerToken();
        if ($token) {
            $payload = self::verifyToken($token);
            if ($payload && isset($payload['sub'])) {
                $db = Database::getInstance();
                $user = $db->fetchOne(
                    "SELECT u.id, u.role_id, r.name as role_name, u.username, u.email, u.avatar, u.status, u.created_at
                     FROM users u
                     JOIN roles r ON u.role_id = r.id
                     WHERE u.id = ? AND u.status = 'active'",
                    [$payload['sub']]
                );
                if ($user) {
                    self::$currentUser = $user;
                    return self::$currentUser;
                }
            }
        }

        return null;
    }

    public static function check(): bool {
        return self::user() !== null;
    }

    public static function id(): ?int {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    public static function isAdmin(): bool {
        $user = self::user();
        return $user && ($user['role_name'] === 'admin' || (int)$user['role_id'] === 1);
    }

    public static function requireAuth(): array {
        $user = self::user();
        if (!$user) {
            Response::error('Vui lòng đăng nhập để tiếp tục', 401);
        }
        return $user;
    }

    public static function requireAdmin(): array {
        $user = self::requireAuth();
        if (!self::isAdmin()) {
            Response::error('Bạn không có quyền truy cập chức năng này', 403);
        }
        return $user;
    }

    public static function loginSession(array $user): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role_name'] = $user['role_name'] ?? 'user';
    }

    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        setcookie('auth_token', '', time() - 3600, '/');
        self::$currentUser = null;
    }

    private static function base64UrlEncode(string $data): string {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    private static function base64UrlDecode(string $data): string {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $data .= str_repeat('=', $padlen);
        }
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }
}
