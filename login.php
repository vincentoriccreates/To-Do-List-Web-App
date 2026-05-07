<?php
/**
 * Login Page
 */
require_once __DIR__ . '/includes/auth.php';
redirect_if_auth();

$error   = '';
$success = $_GET['registered'] ?? false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Security token mismatch. Please try again.';
    } else {
        $result = login_user($_POST['email'] ?? '', $_POST['password'] ?? '');
        if ($result['success']) {
            header('Location: index.php');
            exit;
        }
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,400&display=swap" rel="stylesheet">
<style>
/* ── Reset & Base ──────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --bg:        #F7F5F2;
  --card:      #FFFFFF;
  --ink:       #1A1714;
  --muted:     #7A7570;
  --border:    #E8E4DF;
  --accent:    #2D6A4F;
  --accent-lt: #D8EFE4;
  --danger:    #C0392B;
  --warn:      #E67E22;
  --shadow:    0 4px 24px rgba(26,23,20,.08);
  --radius:    14px;
  --ff-head:   'Syne', sans-serif;
  --ff-body:   'DM Sans', sans-serif;
  transition: background .3s, color .3s;
}

[data-theme="dark"] {
  --bg:      #141210;
  --card:    #1E1B18;
  --ink:     #F0EDE8;
  --muted:   #928D88;
  --border:  #2E2A26;
  --accent:  #52B788;
  --accent-lt: #1A3829;
  --shadow:  0 4px 24px rgba(0,0,0,.4);
}

body {
  font-family: var(--ff-body);
  background: var(--bg);
  color: var(--ink);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
}

/* ── Card ─────────────────────────────────────────── */
.auth-wrap {
  width: 100%;
  max-width: 420px;
  animation: fadeUp .4s ease both;
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-family: var(--ff-head);
  font-size: 26px;
  font-weight: 800;
  color: var(--accent);
  margin-bottom: 32px;
  justify-content: center;
  letter-spacing: -0.5px;
}
.logo-icon {
  width: 36px; height: 36px;
  background: var(--accent);
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
}
.logo-icon svg { width: 20px; height: 20px; fill: none; stroke: #fff; stroke-width: 2.5; stroke-linecap: round; }

.card {
  background: var(--card);
  border-radius: var(--radius);
  border: 1px solid var(--border);
  box-shadow: var(--shadow);
  padding: 40px;
}

h1 {
  font-family: var(--ff-head);
  font-size: 22px;
  font-weight: 700;
  margin-bottom: 4px;
}
.sub { color: var(--muted); font-size: 14px; margin-bottom: 28px; }

/* ── Form ─────────────────────────────────────────── */
.field { margin-bottom: 18px; }
label {
  display: block;
  font-size: 13px;
  font-weight: 500;
  color: var(--muted);
  margin-bottom: 6px;
  letter-spacing: .3px;
  text-transform: uppercase;
}
input[type="email"], input[type="password"], input[type="text"] {
  width: 100%;
  padding: 12px 14px;
  background: var(--bg);
  border: 1.5px solid var(--border);
  border-radius: 10px;
  font-family: var(--ff-body);
  font-size: 15px;
  color: var(--ink);
  outline: none;
  transition: border-color .2s, box-shadow .2s;
}
input:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-lt);
}

.btn-primary {
  width: 100%;
  padding: 13px;
  background: var(--accent);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-family: var(--ff-body);
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: opacity .2s, transform .15s;
  margin-top: 8px;
}
.btn-primary:hover { opacity: .88; transform: translateY(-1px); }
.btn-primary:active { transform: translateY(0); }

.alert {
  padding: 12px 14px;
  border-radius: 10px;
  font-size: 14px;
  margin-bottom: 18px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.alert-error   { background: #FDECEA; color: var(--danger); border: 1px solid #F5C6C2; }
.alert-success { background: var(--accent-lt); color: var(--accent); border: 1px solid #B2DFCC; }

.switch-link {
  text-align: center;
  margin-top: 24px;
  font-size: 14px;
  color: var(--muted);
}
.switch-link a { color: var(--accent); font-weight: 600; text-decoration: none; }
.switch-link a:hover { text-decoration: underline; }

/* ── Dark toggle ──────────────────────────────────── */
.theme-toggle {
  position: fixed; top: 20px; right: 20px;
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 50px;
  padding: 8px 14px;
  cursor: pointer;
  font-size: 13px;
  font-family: var(--ff-body);
  color: var(--muted);
  display: flex; align-items: center; gap: 6px;
  transition: background .2s, color .2s;
  box-shadow: var(--shadow);
}
.theme-toggle:hover { color: var(--ink); }
</style>
</head>
<body>

<button class="theme-toggle" onclick="toggleTheme()" id="themeBtn">🌙 Dark</button>

<div class="auth-wrap">
  <div class="logo">
    <div class="logo-icon">
      <svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
    </div>
    Taskly
  </div>

  <div class="card">
    <h1>Welcome back</h1>
    <p class="sub">Sign in to your account to continue</p>

    <?php if ($error): ?>
      <div class="alert alert-error">⚠️ <?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success">✓ Account created! Sign in below.</div>
    <?php endif; ?>

    <form method="post" action="login.php">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <div class="field">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" placeholder="you@example.com"
               value="<?= e($_POST['email'] ?? '') ?>" required autocomplete="email">
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
      </div>

      <button type="submit" class="btn-primary">Sign In →</button>
    </form>

    <div class="switch-link">
      Don't have an account? <a href="register.php">Create one</a>
    </div>
  </div>
</div>

<script>
function toggleTheme() {
  const html = document.documentElement;
  const isDark = html.getAttribute('data-theme') === 'dark';
  html.setAttribute('data-theme', isDark ? 'light' : 'dark');
  document.getElementById('themeBtn').textContent = isDark ? '🌙 Dark' : '☀️ Light';
  localStorage.setItem('theme', isDark ? 'light' : 'dark');
}
// Restore theme
const saved = localStorage.getItem('theme') || 'light';
document.documentElement.setAttribute('data-theme', saved);
document.getElementById('themeBtn').textContent = saved === 'dark' ? '☀️ Light' : '🌙 Dark';
</script>
</body>
</html>
