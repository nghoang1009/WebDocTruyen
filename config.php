<?php
/**
 * Configuration Settings for WebDocTruyen
 */

// Environment settings
define('APP_ENV', 'development'); // 'development' or 'production'
define('APP_NAME', 'Web Đọc Truyện');
define('APP_URL', 'http://localhost/WebDocTruyen');
define('APP_KEY', 'web_doc_truyen_secure_jwt_secret_key_2026_!#%');

// Database credentials
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'webdoctruyen');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Upload directories
define('ROOT_PATH', __DIR__);
define('PUBLIC_PATH', __DIR__ . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('UPLOAD_URL', APP_URL . '/public/uploads');

// Session config
define('SESSION_LIFETIME', 86400 * 7); // 7 days

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error reporting based on env
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}
