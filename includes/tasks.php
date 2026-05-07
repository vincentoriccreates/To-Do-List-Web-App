<?php
/**
 * Task Helpers
 * All task CRUD operations with prepared statements
 */

require_once __DIR__ . '/database.php';

/**
 * Get all tasks for a user with optional filtering
 */
function get_tasks(int $user_id, string $filter = 'all', string $search = ''): array {
    $sql    = 'SELECT * FROM tasks WHERE user_id = ?';
    $params = [$user_id];

    if ($filter === 'pending') {
        $sql .= ' AND status = "pending"';
    } elseif ($filter === 'completed') {
        $sql .= ' AND status = "completed"';
    }

    if (!empty($search)) {
        $sql    .= ' AND (title LIKE ? OR description LIKE ?)';
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    }

    $sql .= ' ORDER BY FIELD(priority, "high", "medium", "low"), due_date ASC, created_at DESC';

    $stmt = db_query($sql, $params);
    return $stmt->fetchAll();
}

/**
 * Get a single task (verifies ownership)
 */
function get_task(int $task_id, int $user_id): array|false {
    $stmt = db_query('SELECT * FROM tasks WHERE id = ? AND user_id = ?', [$task_id, $user_id]);
    return $stmt->fetch();
}

/**
 * Create a new task
 */
function create_task(int $user_id, array $data): array {
    $title       = trim($data['title'] ?? '');
    $description = trim($data['description'] ?? '');
    $due_date    = !empty($data['due_date']) ? $data['due_date'] : null;
    $priority    = in_array($data['priority'] ?? '', ['low', 'medium', 'high']) ? $data['priority'] : 'medium';

    if (empty($title)) {
        return ['success' => false, 'message' => 'Task title is required.'];
    }
    if (strlen($title) > 255) {
        return ['success' => false, 'message' => 'Title is too long (max 255 chars).'];
    }

    db_query(
        'INSERT INTO tasks (user_id, title, description, due_date, priority) VALUES (?, ?, ?, ?, ?)',
        [$user_id, $title, $description, $due_date, $priority]
    );

    $task_id = db_last_id();
    $task    = get_task((int)$task_id, $user_id);

    return ['success' => true, 'message' => 'Task created.', 'task' => $task];
}

/**
 * Update an existing task
 */
function update_task(int $task_id, int $user_id, array $data): array {
    $task = get_task($task_id, $user_id);
    if (!$task) {
        return ['success' => false, 'message' => 'Task not found.'];
    }

    $title       = trim($data['title'] ?? $task['title']);
    $description = trim($data['description'] ?? $task['description']);
    $due_date    = !empty($data['due_date']) ? $data['due_date'] : null;
    $priority    = in_array($data['priority'] ?? '', ['low', 'medium', 'high']) ? $data['priority'] : $task['priority'];
    $status      = in_array($data['status'] ?? '', ['pending', 'completed']) ? $data['status'] : $task['status'];

    if (empty($title)) {
        return ['success' => false, 'message' => 'Task title is required.'];
    }

    db_query(
        'UPDATE tasks SET title = ?, description = ?, due_date = ?, priority = ?, status = ? WHERE id = ? AND user_id = ?',
        [$title, $description, $due_date, $priority, $status, $task_id, $user_id]
    );

    $updated = get_task($task_id, $user_id);
    return ['success' => true, 'message' => 'Task updated.', 'task' => $updated];
}

/**
 * Toggle task status (pending ↔ completed)
 */
function toggle_task(int $task_id, int $user_id): array {
    $task = get_task($task_id, $user_id);
    if (!$task) {
        return ['success' => false, 'message' => 'Task not found.'];
    }

    $new_status = $task['status'] === 'completed' ? 'pending' : 'completed';
    db_query('UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?', [$new_status, $task_id, $user_id]);

    return ['success' => true, 'status' => $new_status, 'message' => 'Task updated.'];
}

/**
 * Delete a task
 */
function delete_task(int $task_id, int $user_id): array {
    $task = get_task($task_id, $user_id);
    if (!$task) {
        return ['success' => false, 'message' => 'Task not found.'];
    }

    db_query('DELETE FROM tasks WHERE id = ? AND user_id = ?', [$task_id, $user_id]);
    return ['success' => true, 'message' => 'Task deleted.'];
}

/**
 * Get dashboard stats
 */
function get_stats(int $user_id): array {
    $stmt  = db_query('SELECT status, priority, COUNT(*) as count FROM tasks WHERE user_id = ? GROUP BY status, priority', [$user_id]);
    $rows  = $stmt->fetchAll();

    $stats = [
        'total'     => 0,
        'completed' => 0,
        'pending'   => 0,
        'high'      => 0,
        'medium'    => 0,
        'low'       => 0,
    ];

    foreach ($rows as $row) {
        $stats['total']           += $row['count'];
        $stats[$row['status']]    += $row['count'];
        $stats[$row['priority']]  += $row['count'];
    }

    return $stats;
}

/**
 * Update task sort order
 */
function update_task_order(int $user_id, array $ordered_ids): array {
    foreach ($ordered_ids as $position => $task_id) {
        db_query(
            'UPDATE tasks SET sort_order = ? WHERE id = ? AND user_id = ?',
            [$position, (int)$task_id, $user_id]
        );
    }
    return ['success' => true];
}
