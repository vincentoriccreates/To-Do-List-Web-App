<?php
/**
 * Authentication Helpers
 */

require_once __DIR__ . '/database.php';

// ─── Session Bootstrap ────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'secure'   => false,   // Set true in production (HTTPS)
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/**
 * Check if user is logged in
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require authentication – redirect to login if not logged in
 */
function require_auth(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Redirect if already authenticated
 */
function redirect_if_auth(): void {
    if (is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Get current user ID
 */
function current_user_id(): int {
    return (int) ($_SESSION['user_id'] ?? 0);
}

/**
 * Get current user data
 */
function current_user(): array {
    if (!is_logged_in()) return [];
    $stmt = db_query('SELECT id, name, email, created_at FROM users WHERE id = ?', [current_user_id()]);
    return $stmt->fetch() ?: [];
}

/**
 * Register a new user
 */
function register_user(string $name, string $email, string $password): array {
    // Validate
    if (empty($name) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email address.'];
    }
    if (strlen($password) < 8) {
        return ['success' => false, 'message' => 'Password must be at least 8 characters.'];
    }

    // Check duplicate
    $stmt = db_query('SELECT id FROM users WHERE email = ?', [strtolower(trim($email))]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email already registered.'];
    }

    // Insert
    $hash = password_hash($password, PASSWORD_ALGO, PASSWORD_OPTIONS);
    db_query(
        'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)',
        [htmlspecialchars(strip_tags($name)), strtolower(trim($email)), $hash]
    );

    return ['success' => true, 'message' => 'Account created! Please log in.'];
}

/**
 * Log in a user
 */
function login_user(string $email, string $password): array {
    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Email and password are required.'];
    }

    $stmt = db_query('SELECT * FROM users WHERE email = ?', [strtolower(trim($email))]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }

    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['name'];

    return ['success' => true, 'message' => 'Welcome back, ' . htmlspecialchars($user['name']) . '!'];
}

/**
 * Log out
 */
function logout_user(): void {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php');
    exit;
}

/**
 * Sanitize output (XSS prevention)
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
