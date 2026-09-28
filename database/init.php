<?php
/**
 * Database Migration and Seeder Script
 */

require_once __DIR__ . '/../config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // 1. Run schema.sql
    $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schemaSql);

    // Select database
    $pdo->exec("USE `" . DB_NAME . "`;");

    // 2. Run seed.sql
    $seedSql = file_get_contents(__DIR__ . '/seed.sql');
    $pdo->exec($seedSql);

    // 3. Create upload folders and generate real binary covers
    $folders = [
        UPLOAD_PATH,
        UPLOAD_PATH . '/covers',
        UPLOAD_PATH . '/avatars',
    ];
    foreach ($folders as $folder) {
        if (!is_dir($folder)) {
            @mkdir($folder, 0777, true);
        }
    }

    if (file_exists(__DIR__ . '/write_placeholders.php')) {
        require_once __DIR__ . '/write_placeholders.php';
    }
    if (file_exists(__DIR__ . '/create_binary_covers.php')) {
        require_once __DIR__ . '/create_binary_covers.php';
    }

    // Auto redirect to homepage
    if (!headers_sent()) {
        header("Location: " . APP_URL);
        exit;
    } else {
        echo "<script>window.location.href = '" . APP_URL . "';</script>";
        echo "<p>Đang chuyển hướng về <a href='" . APP_URL . "'>Trang chủ</a>...</p>";
        exit;
    }

} catch (PDOException $e) {
    echo "<pre style='font-family: monospace; background: #1e1e1e; color: #ff6b6b; padding: 20px; border-radius: 8px;'>\n";
    echo "[LỖI KẾT NỐI DATABASE]: " . $e->getMessage() . "\n";
    echo "Hãy chắc chắn rằng MySQL trong XAMPP đang được BẬT (Start).\n";
    echo "</pre>";
}
