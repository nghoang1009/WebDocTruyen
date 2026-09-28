<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?Database $instance = null;
    private ?PDO $pdo = null;

    private function __construct() {
        require_once __DIR__ . '/../../config.php';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];

        try {
            // Try connecting to target database
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

            // Verify if tables exist, if not auto-seed
            $checkTable = $this->pdo->query("SHOW TABLES LIKE 'stories'")->fetch();
            if (!$checkTable) {
                $this->autoInitDatabase();
            }
        } catch (PDOException $e) {
            // If database does not exist (error 1049), auto create & seed
            try {
                $serverDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
                $this->pdo = new PDO($serverDsn, DB_USER, DB_PASS, $options);
                $this->autoInitDatabase();
            } catch (PDOException $ex) {
                if (defined('APP_ENV') && APP_ENV === 'development') {
                    die("Database connection failed: " . $ex->getMessage());
                } else {
                    die("Database connection error. Please try again later.");
                }
            }
        }
    }

    private function autoInitDatabase(): void {
        try {
            // 1. Schema
            $schemaFile = __DIR__ . '/../../database/schema.sql';
            if (file_exists($schemaFile)) {
                $schemaSql = file_get_contents($schemaFile);
                $this->pdo->exec($schemaSql);
            }

            // 2. Select database
            $this->pdo->exec("USE `" . DB_NAME . "`;");

            // 3. Seed
            $seedFile = __DIR__ . '/../../database/seed.sql';
            if (file_exists($seedFile)) {
                $seedSql = file_get_contents($seedFile);
                $this->pdo->exec($seedSql);
            }

            // 4. Create upload folders and generate covers
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

            // Generate cover images if missing
            $binGen = __DIR__ . '/../../database/create_binary_covers.php';
            if (file_exists($binGen)) {
                require_once $binGen;
            }
            $phGen = __DIR__ . '/../../database/write_placeholders.php';
            if (file_exists($phGen)) {
                require_once $phGen;
            }
        } catch (\Throwable $t) {
            // Silently log or continue
            error_log("Auto init database warning: " . $t->getMessage());
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }

    public function query(string $sql, array $params = []): \PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchColumn(string $sql, array $params = []) {
        return $this->query($sql, $params)->fetchColumn();
    }

    public function lastInsertId(): string {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool {
        return $this->pdo->commit();
    }

    public function rollBack(): bool {
        return $this->pdo->rollBack();
    }
}
