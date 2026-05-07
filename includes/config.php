<?php
/**
 * Configuration File
 * Contains database credentials and app settings
 */

// ─── Database Configuration ───────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'todo_app');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ─── App Configuration ────────────────────────────────────────────────────────
define('APP_NAME', 'Taskly');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/todo-app');

// ─── Session Configuration ────────────────────────────────────────────────────
define('SESSION_LIFETIME', 86400); // 24 hours

// ─── Security ─────────────────────────────────────────────────────────────────
define('PASSWORD_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_OPTIONS', ['cost' => 12]);

// ─── Timezone ─────────────────────────────────────────────────────────────────
date_default_timezone_set('UTC');

// ─── Error Reporting (set to 0 in production) ────────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', 1);
