<?php
/**
 * Tasks API
 * Handles AJAX requests for task CRUD, toggle, reorder
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/tasks.php';

header('Content-Type: application/json');

// Must be logged in
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = current_user_id();
$method  = $_SERVER['REQUEST_METHOD'];
$action  = $_GET['action'] ?? '';

// Parse JSON body for POST/PUT/PATCH
$body = [];
if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
    $raw  = file_get_contents('php://input');
    $body = json_decode($raw, true) ?? $_POST;
}

// ─── Verify CSRF for state-changing operations ────────────────────────────────
if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
    $token = $body['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!verify_csrf($token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
}

// ─── Route ────────────────────────────────────────────────────────────────────
try {
    switch (true) {

        // GET /api/tasks.php?action=list
        case $method === 'GET' && $action === 'list':
            $filter = $_GET['filter'] ?? 'all';
            $search = trim($_GET['search'] ?? '');
            $tasks  = get_tasks($user_id, $filter, $search);
            echo json_encode(['success' => true, 'tasks' => $tasks]);
            break;

        // GET /api/tasks.php?action=stats
        case $method === 'GET' && $action === 'stats':
            $stats = get_stats($user_id);
            echo json_encode(['success' => true, 'stats' => $stats]);
            break;

        // POST /api/tasks.php?action=create
        case $method === 'POST' && $action === 'create':
            $result = create_task($user_id, $body);
            echo json_encode($result);
            break;

        // POST /api/tasks.php?action=update
        case $method === 'POST' && $action === 'update':
            $task_id = (int)($body['id'] ?? 0);
            $result  = update_task($task_id, $user_id, $body);
            echo json_encode($result);
            break;

        // POST /api/tasks.php?action=toggle
        case $method === 'POST' && $action === 'toggle':
            $task_id = (int)($body['id'] ?? 0);
            $result  = toggle_task($task_id, $user_id);
            echo json_encode($result);
            break;

        // POST /api/tasks.php?action=delete
        case $method === 'POST' && $action === 'delete':
            $task_id = (int)($body['id'] ?? 0);
            $result  = delete_task($task_id, $user_id);
            echo json_encode($result);
            break;

        // POST /api/tasks.php?action=reorder
        case $method === 'POST' && $action === 'reorder':
            $ids    = array_map('intval', $body['ids'] ?? []);
            $result = update_task_order($user_id, $ids);
            echo json_encode($result);
            break;

        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
            break;
    }
} catch (Exception $e) {
    error_log('API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
