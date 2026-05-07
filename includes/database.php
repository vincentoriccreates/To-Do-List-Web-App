<?php
/**
 * Database Connection
 * Singleton PDO instance with prepared statements support
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    /**
     * Get PDO instance (singleton)
     */
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST, DB_NAME, DB_CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Log error in production; display in dev
                error_log('Database connection failed: ' . $e->getMessage());
                http_response_code(500);
                die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
            }
        }

        return self::$instance;
    }

    // Prevent instantiation and cloning
    private function __construct() {}
    private function __clone() {}
}

/**
 * Helper: Execute a prepared statement and return the PDOStatement
 */
function db_query(string $sql, array $params = []): PDOStatement {
    $pdo  = Database::getInstance();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Helper: Last inserted ID
 */
function db_last_id(): string {
    return Database::getInstance()->lastInsertId();
}
