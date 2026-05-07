<?php
/**
 * Registration Page
 */
require_once __DIR__ . '/includes/auth.php';
redirect_if_auth();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Security token mismatch. Please try again.';
    } else {
        $result = register_user(
            $_POST['name']     ?? '',
            $_POST['email']    ?? '',
            $_POST['password'] ?? ''
        );
        if ($result['success']) {
            header('Location: login.php?registered=1');
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
<title>Create Account — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,400&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --bg:#F7F5F2;--card:#FFFFFF;--ink:#1A1714;--muted:#7A7570;--border:#E8E4DF;
  --accent:#2D6A4F;--accent-lt:#D8EFE4;--danger:#C0392B;
  --shadow:0 4px 24px rgba(26,23,20,.08);--radius:14px;
  --ff-head:'Syne',sans-serif;--ff-body:'DM Sans',sans-serif;
}
[data-theme="dark"]{--bg:#141210;--card:#1E1B18;--ink:#F0EDE8;--muted:#928D88;--border:#2E2A26;--accent:#52B788;--accent-lt:#1A3829;--shadow:0 4px 24px rgba(0,0,0,.4);}
body{font-family:var(--ff-body);background:var(--bg);color:var(--ink);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}
.auth-wrap{width:100%;max-width:440px;animation:fadeUp .4s ease both;}
@keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
.logo{display:flex;align-items:center;gap:10px;font-family:var(--ff-head);font-size:26px;font-weight:800;color:var(--accent);margin-bottom:32px;justify-content:center;letter-spacing:-0.5px;}
.logo-icon{width:36px;height:36px;background:var(--accent);border-radius:10px;display:flex;align-items:center;justify-content:center;}
.logo-icon svg{width:20px;height:20px;fill:none;stroke:#fff;stroke-width:2.5;stroke-linecap:round;}
.card{background:var(--card);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);padding:40px;}
h1{font-family:var(--ff-head);font-size:22px;font-weight:700;margin-bottom:4px;}
.sub{color:var(--muted);font-size:14px;margin-bottom:28px;}
.field{margin-bottom:18px;}
.field-row{display:grid;gap:14px;}
label{display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:6px;letter-spacing:.3px;text-transform:uppercase;}
input{width:100%;padding:12px 14px;background:var(--bg);border:1.5px solid var(--border);border-radius:10px;font-family:var(--ff-body);font-size:15px;color:var(--ink);outline:none;transition:border-color .2s,box-shadow .2s;}
input:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-lt);}
.btn-primary{width:100%;padding:13px;background:var(--accent);color:#fff;border:none;border-radius:10px;font-family:var(--ff-body);font-size:15px;font-weight:600;cursor:pointer;transition:opacity .2s,transform .15s;margin-top:8px;}
.btn-primary:hover{opacity:.88;transform:translateY(-1px);}
.alert{padding:12px 14px;border-radius:10px;font-size:14px;margin-bottom:18px;}
.alert-error{background:#FDECEA;color:var(--danger);border:1px solid #F5C6C2;}
.switch-link{text-align:center;margin-top:24px;font-size:14px;color:var(--muted);}
.switch-link a{color:var(--accent);font-weight:600;text-decoration:none;}
.hint{font-size:12px;color:var(--muted);margin-top:4px;}
.theme-toggle{position:fixed;top:20px;right:20px;background:var(--card);border:1px solid var(--border);border-radius:50px;padding:8px 14px;cursor:pointer;font-size:13px;font-family:var(--ff-body);color:var(--muted);display:flex;align-items:center;gap:6px;box-shadow:var(--shadow);}
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
    <h1>Create your account</h1>
    <p class="sub">Start managing your tasks today — it's free</p>

    <?php if ($error): ?>
      <div class="alert alert-error">⚠️ <?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="register.php">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <div class="field">
        <label>Your name</label>
        <input type="text" name="name" placeholder="Jane Smith"
               value="<?= e($_POST['name'] ?? '') ?>" required autocomplete="name">
      </div>

      <div class="field">
        <label>Email address</label>
        <input type="email" name="email" placeholder="you@example.com"
               value="<?= e($_POST['email'] ?? '') ?>" required autocomplete="email">
      </div>

      <div class="field">
        <label>Password</label>
        <input type="password" name="password" placeholder="Min. 8 characters" required minlength="8">
        <p class="hint">At least 8 characters</p>
      </div>

      <button type="submit" class="btn-primary">Create Account →</button>
    </form>

    <div class="switch-link">
      Already have an account? <a href="login.php">Sign in</a>
    </div>
  </div>
</div>

<script>
function toggleTheme(){const h=document.documentElement,d=h.getAttribute('data-theme')==='dark';h.setAttribute('data-theme',d?'light':'dark');document.getElementById('themeBtn').textContent=d?'🌙 Dark':'☀️ Light';localStorage.setItem('theme',d?'light':'dark');}
const saved=localStorage.getItem('theme')||'light';document.documentElement.setAttribute('data-theme',saved);document.getElementById('themeBtn').textContent=saved==='dark'?'☀️ Light':'🌙 Dark';
</script>
</body>
</html>
