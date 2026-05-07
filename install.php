<?php
/**
 * Taskly Installer
 * Run this once to create the database and tables.
 * DELETE or restrict access to this file after setup!
 */

$step    = $_POST['step'] ?? 'form';
$message = '';
$success = false;

if ($step === 'install' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $host   = trim($_POST['db_host']   ?? 'localhost');
    $name   = trim($_POST['db_name']   ?? 'todo_app');
    $user   = trim($_POST['db_user']   ?? 'root');
    $pass   = $_POST['db_pass']        ?? '';
    $dbport = trim($_POST['db_port']   ?? '3306');

    try {
        // Connect without DB name first
        $pdo = new PDO(
            "mysql:host={$host};port={$dbport};charset=utf8mb4",
            $user, $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // Create database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$name}`");

        // Create tables
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `users` (
                `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name`          VARCHAR(100) NOT NULL,
                `email`         VARCHAR(255) NOT NULL UNIQUE,
                `password_hash` VARCHAR(255) NOT NULL,
                `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX `idx_email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `tasks` (
                `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id`     INT UNSIGNED NOT NULL,
                `title`       VARCHAR(255) NOT NULL,
                `description` TEXT NULL,
                `due_date`    DATE NULL,
                `priority`    ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
                `status`      ENUM('pending','completed') NOT NULL DEFAULT 'pending',
                `sort_order`  INT UNSIGNED NOT NULL DEFAULT 0,
                `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk_tasks_user`
                    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                INDEX `idx_user_status` (`user_id`, `status`),
                INDEX `idx_user_priority` (`user_id`, `priority`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Update config.php
        $configPath = __DIR__ . '/includes/config.php';
        if (is_writable($configPath)) {
            $config = file_get_contents($configPath);
            $config = preg_replace("/define\('DB_HOST',\s*'[^']*'\)/", "define('DB_HOST', '{$host}')", $config);
            $config = preg_replace("/define\('DB_NAME',\s*'[^']*'\)/", "define('DB_NAME', '{$name}')", $config);
            $config = preg_replace("/define\('DB_USER',\s*'[^']*'\)/", "define('DB_USER', '{$user}')", $config);
            $config = preg_replace("/define\('DB_PASS',\s*'[^']*'\)/", "define('DB_PASS', '{$pass}')", $config);
            file_put_contents($configPath, $config);
        }

        $success = true;
        $message = 'Installation complete! Database and tables created successfully.';

    } catch (PDOException $e) {
        $message = 'Database error: ' . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Taskly — Installer</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#F7F5F2;color:#1A1714;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}
.wrap{width:100%;max-width:460px;}
.logo{display:flex;align-items:center;gap:10px;font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:#2D6A4F;margin-bottom:32px;justify-content:center;}
.logo-icon{width:36px;height:36px;background:#2D6A4F;border-radius:10px;display:flex;align-items:center;justify-content:center;}
.logo-icon svg{width:20px;height:20px;fill:none;stroke:#fff;stroke-width:2.5;stroke-linecap:round;}
.card{background:#fff;border-radius:16px;border:1px solid #E8E4DF;box-shadow:0 4px 24px rgba(26,23,20,.09);padding:40px;}
h1{font-family:'Syne',sans-serif;font-size:20px;font-weight:700;margin-bottom:6px;}
.sub{color:#7A7570;font-size:14px;margin-bottom:28px;}
.field{margin-bottom:18px;}
label{display:block;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#7A7570;margin-bottom:6px;}
input{width:100%;padding:11px 14px;background:#F7F5F2;border:1.5px solid #E8E4DF;border-radius:10px;font-size:15px;outline:none;transition:border-color .2s;}
input:focus{border-color:#2D6A4F;background:#fff;}
.btn{width:100%;padding:13px;background:#2D6A4F;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;margin-top:8px;transition:opacity .2s;}
.btn:hover{opacity:.88;}
.alert{padding:14px 16px;border-radius:10px;font-size:14px;margin-bottom:20px;line-height:1.5;}
.alert-ok{background:#D8EFE4;color:#1B5E3A;border:1px solid #A8D5B8;}
.alert-err{background:#FDECEA;color:#C0392B;border:1px solid #F5C6C2;}
.warning{background:#FFF8E7;border:1px solid #FFE082;border-radius:10px;padding:12px 16px;font-size:13px;color:#7A5F00;margin-top:20px;}
.field-row{display:grid;grid-template-columns:2fr 1fr;gap:12px;}
.go-link{display:block;text-align:center;margin-top:20px;color:#2D6A4F;font-weight:600;text-decoration:none;font-size:15px;}
</style>
</head>
<body>
<div class="wrap">
  <div class="logo">
    <div class="logo-icon"><svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
    Taskly Installer
  </div>

  <div class="card">
    <?php if ($success): ?>
      <div class="alert alert-ok">✓ <?= htmlspecialchars($message) ?></div>
      <p style="font-size:14px;color:#4A4540;margin-bottom:16px;">Your database has been set up. You can now register an account and start using Taskly.</p>
      <a href="register.php" class="btn" style="display:block;text-align:center;text-decoration:none;">Go to App →</a>
      <div class="warning">⚠ <strong>Security:</strong> Delete or rename <code>install.php</code> now that setup is complete.</div>

    <?php else: ?>
      <h1>Database Setup</h1>
      <p class="sub">Enter your MySQL credentials to create the database and tables.</p>

      <?php if ($message): ?>
        <div class="alert alert-err">⚠ <?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <form method="post" action="install.php">
        <input type="hidden" name="step" value="install">

        <div class="field field-row">
          <div>
            <label>Database Host</label>
            <input type="text" name="db_host" value="localhost" required>
          </div>
          <div>
            <label>Port</label>
            <input type="text" name="db_port" value="3306" required>
          </div>
        </div>

        <div class="field">
          <label>Database Name</label>
          <input type="text" name="db_name" value="todo_app" required>
        </div>

        <div class="field">
          <label>MySQL Username</label>
          <input type="text" name="db_user" value="root" required>
        </div>

        <div class="field">
          <label>MySQL Password</label>
          <input type="password" name="db_pass" placeholder="Leave blank if none">
        </div>

        <button type="submit" class="btn">Run Installation →</button>
      </form>

      <div class="warning">
        ⚠ <strong>Note:</strong> This installer will create the database, tables, and update <code>includes/config.php</code> automatically.
        Delete <code>install.php</code> after setup for security.
      </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
