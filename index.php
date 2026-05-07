<?php
/**
 * Main Dashboard
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/tasks.php';
require_auth();

$user  = current_user();
$stats = get_stats(current_user_id());
$csrf  = csrf_token();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= APP_NAME ?> — My Tasks</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
<style>
/* ════════════════════════════════════════════════════
   TASKLY – Main Stylesheet
   ════════════════════════════════════════════════════ */

/* ── Design Tokens ─────────────────────────────────── */
:root {
  --bg:          #F7F5F2;
  --surface:     #FFFFFF;
  --surface-2:   #F0EDE8;
  --ink:         #1A1714;
  --ink-2:       #4A4540;
  --muted:       #9A9590;
  --border:      #E8E4DF;
  --accent:      #2D6A4F;
  --accent-lt:   #D8EFE4;
  --accent-glow: rgba(45,106,79,.15);
  --danger:      #C0392B;
  --danger-lt:   #FDECEA;
  --warn:        #D68910;
  --warn-lt:     #FEF9E7;
  --info:        #2471A3;
  --info-lt:     #EBF5FB;
  --shadow-sm:   0 1px 4px rgba(26,23,20,.06);
  --shadow:      0 4px 20px rgba(26,23,20,.09);
  --shadow-lg:   0 12px 48px rgba(26,23,20,.14);
  --radius-sm:   8px;
  --radius:      12px;
  --radius-lg:   18px;
  --sidebar-w:   260px;
  --ff-head:     'Syne', sans-serif;
  --ff-body:     'DM Sans', sans-serif;
  --transition:  .2s cubic-bezier(.4,0,.2,1);
}

[data-theme="dark"] {
  --bg:          #0F0D0B;
  --surface:     #1A1714;
  --surface-2:   #231F1B;
  --ink:         #F0EDE8;
  --ink-2:       #C0BBB5;
  --muted:       #7A7570;
  --border:      #2E2A26;
  --accent:      #52B788;
  --accent-lt:   #1A3829;
  --accent-glow: rgba(82,183,136,.15);
  --danger:      #E74C3C;
  --danger-lt:   #2C1210;
  --warn:        #F39C12;
  --warn-lt:     #2C2008;
  --info:        #3498DB;
  --info-lt:     #0D1E2C;
  --shadow-sm:   0 1px 4px rgba(0,0,0,.3);
  --shadow:      0 4px 20px rgba(0,0,0,.4);
  --shadow-lg:   0 12px 48px rgba(0,0,0,.6);
}

/* ── Reset ─────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
  font-family: var(--ff-body);
  background: var(--bg);
  color: var(--ink);
  line-height: 1.5;
  transition: background var(--transition), color var(--transition);
}
a { color: inherit; text-decoration: none; }
button { cursor: pointer; font-family: var(--ff-body); }
input, textarea, select { font-family: var(--ff-body); }
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

/* ── Layout ────────────────────────────────────────── */
.app {
  display: flex;
  min-height: 100vh;
}

/* ── Sidebar ───────────────────────────────────────── */
.sidebar {
  width: var(--sidebar-w);
  background: var(--surface);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0; left: 0;
  height: 100vh;
  z-index: 100;
  transition: transform var(--transition), background var(--transition);
}

.sidebar-head {
  padding: 28px 24px 20px;
  border-bottom: 1px solid var(--border);
}
.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-family: var(--ff-head);
  font-size: 22px;
  font-weight: 800;
  color: var(--accent);
  letter-spacing: -.5px;
}
.logo-icon {
  width: 34px; height: 34px;
  background: var(--accent);
  border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.logo-icon svg { width: 18px; height: 18px; fill: none; stroke: #fff; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

/* Stats strip in sidebar */
.sidebar-stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  padding: 16px 24px;
  border-bottom: 1px solid var(--border);
}
.stat-chip {
  background: var(--surface-2);
  border-radius: var(--radius-sm);
  padding: 10px 12px;
  text-align: center;
}
.stat-chip .num {
  font-family: var(--ff-head);
  font-size: 22px;
  font-weight: 700;
  line-height: 1;
  color: var(--ink);
}
.stat-chip .lbl {
  font-size: 11px;
  color: var(--muted);
  margin-top: 2px;
  text-transform: uppercase;
  letter-spacing: .5px;
}
.stat-chip.done .num { color: var(--accent); }

/* Nav */
.sidebar-nav {
  padding: 16px 12px;
  flex: 1;
}
.nav-label {
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: var(--muted);
  padding: 0 12px;
  margin-bottom: 8px;
  margin-top: 12px;
}
.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: var(--radius-sm);
  font-size: 14px;
  font-weight: 500;
  color: var(--ink-2);
  cursor: pointer;
  transition: background var(--transition), color var(--transition);
  border: none;
  background: none;
  width: 100%;
  text-align: left;
}
.nav-item svg { width: 16px; height: 16px; flex-shrink: 0; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.nav-item:hover { background: var(--surface-2); color: var(--ink); }
.nav-item.active { background: var(--accent-lt); color: var(--accent); font-weight: 600; }
.nav-item .badge {
  margin-left: auto;
  background: var(--accent);
  color: #fff;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  padding: 1px 7px;
  min-width: 22px;
  text-align: center;
}

/* Priority nav dots */
.priority-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}
.priority-dot.high   { background: var(--danger); }
.priority-dot.medium { background: var(--warn); }
.priority-dot.low    { background: var(--info); }

.sidebar-foot {
  padding: 16px 12px;
  border-top: 1px solid var(--border);
}
.user-block {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: var(--radius-sm);
  background: var(--surface-2);
}
.avatar {
  width: 32px; height: 32px;
  background: var(--accent);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--ff-head);
  font-weight: 700;
  font-size: 13px;
  color: #fff;
  flex-shrink: 0;
}
.user-name { font-size: 13px; font-weight: 600; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.user-email { font-size: 11px; color: var(--muted); }
.logout-btn {
  background: none; border: none; padding: 4px;
  color: var(--muted);
  border-radius: 6px;
  display: flex; align-items: center;
  transition: color var(--transition);
}
.logout-btn:hover { color: var(--danger); }
.logout-btn svg { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

/* ── Main ──────────────────────────────────────────── */
.main {
  margin-left: var(--sidebar-w);
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* Topbar */
.topbar {
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  padding: 16px 32px;
  display: flex;
  align-items: center;
  gap: 16px;
  position: sticky;
  top: 0;
  z-index: 50;
}
.topbar-title {
  font-family: var(--ff-head);
  font-size: 18px;
  font-weight: 700;
  flex: 1;
}

.search-wrap {
  position: relative;
  flex: 1;
  max-width: 400px;
}
.search-wrap svg {
  position: absolute;
  left: 12px; top: 50%; transform: translateY(-50%);
  width: 16px; height: 16px;
  fill: none; stroke: var(--muted); stroke-width: 2; stroke-linecap: round;
  pointer-events: none;
}
.search-input {
  width: 100%;
  padding: 9px 12px 9px 36px;
  background: var(--surface-2);
  border: 1.5px solid transparent;
  border-radius: 40px;
  font-size: 14px;
  color: var(--ink);
  outline: none;
  transition: border-color var(--transition), background var(--transition);
}
.search-input::placeholder { color: var(--muted); }
.search-input:focus { background: var(--surface); border-color: var(--accent); }

.topbar-actions { display: flex; align-items: center; gap: 8px; }

.theme-btn {
  background: var(--surface-2);
  border: 1px solid var(--border);
  border-radius: 50%;
  width: 36px; height: 36px;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px;
  transition: background var(--transition);
}
.theme-btn:hover { background: var(--accent-lt); }

.hamburger {
  display: none;
  background: none; border: none;
  flex-direction: column; gap: 4px; padding: 4px;
}
.hamburger span { display: block; width: 20px; height: 2px; background: var(--ink); border-radius: 2px; transition: var(--transition); }

/* Content */
.content {
  padding: 32px;
  flex: 1;
}

/* Add Task Form */
.add-task-card {
  background: var(--surface);
  border: 1.5px solid var(--border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
  padding: 20px 24px;
  margin-bottom: 28px;
  transition: border-color var(--transition), box-shadow var(--transition);
}
.add-task-card:focus-within {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-glow), var(--shadow);
}

.add-task-top {
  display: flex;
  gap: 12px;
  align-items: center;
}
.task-title-input {
  flex: 1;
  border: none;
  background: none;
  font-size: 16px;
  font-weight: 500;
  color: var(--ink);
  outline: none;
  padding: 4px 0;
}
.task-title-input::placeholder { color: var(--muted); font-weight: 400; }

.add-expand-btn {
  background: none; border: none;
  color: var(--muted);
  display: flex; align-items: center;
  font-size: 13px; gap: 4px;
  padding: 6px 10px;
  border-radius: var(--radius-sm);
  transition: color var(--transition), background var(--transition);
}
.add-expand-btn:hover { color: var(--ink); background: var(--surface-2); }
.add-expand-btn svg { width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 2; }

.add-task-extras {
  overflow: hidden;
  max-height: 0;
  transition: max-height .3s ease, opacity .2s ease;
  opacity: 0;
}
.add-task-extras.open { max-height: 300px; opacity: 1; }
.add-task-extras-inner {
  padding-top: 16px;
  border-top: 1px solid var(--border);
  margin-top: 14px;
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 12px;
  align-items: start;
}
.task-desc-input {
  grid-column: 1 / -1;
  width: 100%;
  padding: 10px 12px;
  background: var(--surface-2);
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-size: 14px;
  color: var(--ink);
  resize: vertical;
  min-height: 80px;
  outline: none;
  transition: border-color var(--transition);
}
.task-desc-input:focus { border-color: var(--accent); }
.task-desc-input::placeholder { color: var(--muted); }

select, .date-input {
  width: 100%;
  padding: 9px 12px;
  background: var(--surface-2);
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-size: 14px;
  color: var(--ink);
  outline: none;
  transition: border-color var(--transition);
  appearance: none;
  -webkit-appearance: none;
}
select:focus, .date-input:focus { border-color: var(--accent); background: var(--surface); }
.date-input { color-scheme: light dark; }

.form-field-wrap { position: relative; }
.form-field-wrap label {
  display: block; font-size: 11px; font-weight: 600;
  text-transform: uppercase; letter-spacing: .5px;
  color: var(--muted); margin-bottom: 5px;
}

.add-task-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 14px;
}
.btn-ghost {
  background: none;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 8px 16px;
  font-size: 14px;
  font-weight: 500;
  color: var(--ink-2);
  transition: var(--transition);
}
.btn-ghost:hover { border-color: var(--ink-2); color: var(--ink); background: var(--surface-2); }
.btn-accent {
  background: var(--accent);
  border: none;
  border-radius: var(--radius-sm);
  padding: 8px 20px;
  font-size: 14px;
  font-weight: 600;
  color: #fff;
  display: flex; align-items: center; gap: 6px;
  transition: opacity var(--transition), transform .15s;
}
.btn-accent:hover { opacity: .88; transform: translateY(-1px); }
.btn-accent:disabled { opacity: .5; pointer-events: none; }
.btn-accent svg { width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 2.5; stroke-linecap: round; }

/* ── Filters ───────────────────────────────────────── */
.filter-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.filter-bar-label { font-size: 13px; color: var(--muted); margin-right: 4px; }
.filter-btn {
  background: var(--surface);
  border: 1.5px solid var(--border);
  border-radius: 40px;
  padding: 7px 16px;
  font-size: 13px;
  font-weight: 500;
  color: var(--ink-2);
  transition: var(--transition);
}
.filter-btn:hover { border-color: var(--accent); color: var(--accent); }
.filter-btn.active { background: var(--accent); border-color: var(--accent); color: #fff; }

.sort-select {
  margin-left: auto;
  padding: 7px 12px;
  background: var(--surface);
  border: 1.5px solid var(--border);
  border-radius: 40px;
  font-size: 13px;
  color: var(--ink-2);
  outline: none;
  appearance: none;
  cursor: pointer;
  transition: var(--transition);
}

/* ── Task List ─────────────────────────────────────── */
#task-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* Task Card */
.task-card {
  background: var(--surface);
  border: 1.5px solid var(--border);
  border-radius: var(--radius);
  padding: 16px 20px;
  display: flex;
  align-items: flex-start;
  gap: 14px;
  transition: box-shadow var(--transition), border-color var(--transition), transform .15s;
  cursor: grab;
  animation: slideIn .25s ease both;
  position: relative;
  overflow: hidden;
}
.task-card::before {
  content: '';
  position: absolute;
  left: 0; top: 0; bottom: 0;
  width: 3px;
  border-radius: 3px 0 0 3px;
}
.task-card.priority-high::before   { background: var(--danger); }
.task-card.priority-medium::before { background: var(--warn); }
.task-card.priority-low::before    { background: var(--info); }

@keyframes slideIn {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.task-card:hover {
  box-shadow: var(--shadow);
  border-color: var(--accent);
  transform: translateY(-1px);
}
.task-card.completed { opacity: .7; }
.task-card.dragging { opacity: .5; box-shadow: var(--shadow-lg); transform: rotate(1deg); }
.task-card.drag-over { border-color: var(--accent); background: var(--accent-lt); }

/* Checkbox */
.task-check {
  width: 20px; height: 20px;
  border: 2px solid var(--border);
  border-radius: 50%;
  flex-shrink: 0;
  margin-top: 2px;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: var(--transition);
  background: none;
}
.task-check:hover { border-color: var(--accent); background: var(--accent-lt); }
.task-check.checked {
  background: var(--accent);
  border-color: var(--accent);
}
.task-check.checked svg { display: block; }
.task-check svg { display: none; width: 10px; height: 10px; fill: none; stroke: #fff; stroke-width: 3; stroke-linecap: round; stroke-linejoin: round; }

/* Task body */
.task-body { flex: 1; min-width: 0; }
.task-title {
  font-size: 15px;
  font-weight: 600;
  color: var(--ink);
  margin-bottom: 4px;
  line-height: 1.4;
  transition: color var(--transition);
}
.task-card.completed .task-title {
  text-decoration: line-through;
  color: var(--muted);
}
.task-desc {
  font-size: 13px;
  color: var(--ink-2);
  margin-bottom: 8px;
  line-height: 1.5;
  white-space: pre-wrap;
}
.task-card.completed .task-desc { color: var(--muted); }

.task-meta {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
.meta-chip {
  display: flex; align-items: center; gap: 4px;
  font-size: 11px;
  padding: 3px 8px;
  border-radius: 20px;
  font-weight: 500;
  letter-spacing: .2px;
}
.chip-priority-high   { background: var(--danger-lt); color: var(--danger); }
.chip-priority-medium { background: var(--warn-lt);   color: var(--warn); }
.chip-priority-low    { background: var(--info-lt);   color: var(--info); }
.chip-date { background: var(--surface-2); color: var(--muted); }
.chip-date.overdue { background: var(--danger-lt); color: var(--danger); }
.chip-status-completed { background: var(--accent-lt); color: var(--accent); }

.meta-chip svg { width: 10px; height: 10px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; }

/* Task actions */
.task-actions {
  display: flex;
  gap: 4px;
  opacity: 0;
  transition: opacity var(--transition);
}
.task-card:hover .task-actions { opacity: 1; }
.act-btn {
  background: none; border: none;
  width: 30px; height: 30px;
  border-radius: var(--radius-sm);
  display: flex; align-items: center; justify-content: center;
  color: var(--muted);
  transition: var(--transition);
}
.act-btn svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.act-btn:hover { background: var(--surface-2); color: var(--ink); }
.act-btn.delete:hover { background: var(--danger-lt); color: var(--danger); }

/* Drag handle */
.drag-handle {
  color: var(--muted);
  cursor: grab;
  display: flex; align-items: center;
  opacity: 0;
  transition: opacity var(--transition);
  padding: 0 4px;
  margin-top: 2px;
}
.task-card:hover .drag-handle { opacity: 1; }
.drag-handle svg { width: 14px; height: 14px; fill: var(--muted); }

/* Empty state */
.empty-state {
  text-align: center;
  padding: 80px 32px;
  animation: fadeUp .4s ease;
}
.empty-icon {
  width: 72px; height: 72px;
  background: var(--surface-2);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 20px;
}
.empty-icon svg { width: 32px; height: 32px; fill: none; stroke: var(--muted); stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round; }
.empty-state h3 { font-family: var(--ff-head); font-size: 20px; font-weight: 700; margin-bottom: 8px; }
.empty-state p { color: var(--muted); font-size: 14px; max-width: 280px; margin: 0 auto; }

/* Loading spinner */
.spinner {
  width: 28px; height: 28px;
  border: 3px solid var(--border);
  border-top-color: var(--accent);
  border-radius: 50%;
  animation: spin .7s linear infinite;
  margin: 40px auto;
}
@keyframes spin { to { transform: rotate(360deg); } }
@keyframes fadeUp { from { opacity:0;transform:translateY(12px) } to { opacity:1;transform:translateY(0) } }

/* ── Modal ─────────────────────────────────────────── */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,.45);
  backdrop-filter: blur(4px);
  z-index: 1000;
  display: flex; align-items: center; justify-content: center;
  padding: 20px;
  animation: fadeOverlay .2s ease;
}
@keyframes fadeOverlay { from { opacity: 0 } to { opacity: 1 } }
.modal-overlay.hidden { display: none; }

.modal {
  background: var(--surface);
  border-radius: var(--radius-lg);
  border: 1px solid var(--border);
  box-shadow: var(--shadow-lg);
  width: 100%;
  max-width: 520px;
  animation: modalIn .25s cubic-bezier(.34,1.56,.64,1) both;
  overflow: hidden;
}
@keyframes modalIn { from { opacity:0;transform:scale(.93) } to { opacity:1;transform:scale(1) } }

.modal-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 22px 24px;
  border-bottom: 1px solid var(--border);
}
.modal-title { font-family: var(--ff-head); font-size: 17px; font-weight: 700; }
.modal-close {
  background: none; border: none;
  color: var(--muted);
  width: 30px; height: 30px;
  border-radius: var(--radius-sm);
  display: flex; align-items: center; justify-content: center;
  transition: var(--transition);
}
.modal-close:hover { background: var(--danger-lt); color: var(--danger); }
.modal-close svg { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2.5; stroke-linecap: round; }

.modal-body { padding: 24px; }
.modal-field { margin-bottom: 18px; }
.modal-field label {
  display: block;
  font-size: 12px; font-weight: 600;
  text-transform: uppercase; letter-spacing: .5px;
  color: var(--muted); margin-bottom: 6px;
}
.modal-input, .modal-textarea, .modal-select, .modal-date {
  width: 100%;
  padding: 10px 14px;
  background: var(--surface-2);
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-size: 15px;
  color: var(--ink);
  outline: none;
  transition: border-color var(--transition), background var(--transition);
  appearance: none;
}
.modal-input:focus, .modal-textarea:focus, .modal-select:focus, .modal-date:focus {
  border-color: var(--accent);
  background: var(--surface);
  box-shadow: 0 0 0 3px var(--accent-glow);
}
.modal-textarea { resize: vertical; min-height: 90px; }
.modal-date { color-scheme: light dark; }
.modal-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

.modal-foot {
  padding: 0 24px 24px;
  display: flex; justify-content: flex-end; gap: 10px;
}

/* ── Toast ─────────────────────────────────────────── */
.toast-container {
  position: fixed; bottom: 24px; right: 24px;
  z-index: 9999;
  display: flex; flex-direction: column; gap: 10px;
}
.toast {
  background: var(--ink);
  color: var(--bg);
  padding: 12px 18px;
  border-radius: var(--radius-sm);
  font-size: 14px;
  font-weight: 500;
  display: flex; align-items: center; gap: 10px;
  box-shadow: var(--shadow-lg);
  animation: toastIn .3s cubic-bezier(.34,1.56,.64,1) both;
  max-width: 320px;
}
.toast.success { background: var(--accent); color: #fff; }
.toast.error   { background: var(--danger); color: #fff; }
.toast.exit    { animation: toastOut .3s ease forwards; }
@keyframes toastIn  { from { opacity:0;transform:translateX(20px) } to { opacity:1;transform:translateX(0) } }
@keyframes toastOut { to   { opacity:0;transform:translateX(20px) } }

/* ── Responsive ────────────────────────────────────── */
@media (max-width: 768px) {
  :root { --sidebar-w: 260px; }
  .sidebar { transform: translateX(-100%); }
  .sidebar.open { transform: translateX(0); box-shadow: var(--shadow-lg); }
  .main { margin-left: 0; }
  .hamburger { display: flex; }
  .content { padding: 20px 16px; }
  .topbar { padding: 14px 16px; }
  .add-task-extras-inner { grid-template-columns: 1fr 1fr; }
  .modal-row { grid-template-columns: 1fr; }
  .task-actions { opacity: 1; }
  .drag-handle { display: none; }
}
.sidebar-overlay {
  display: none;
  position: fixed; inset: 0;
  background: rgba(0,0,0,.4);
  z-index: 99;
}
.sidebar-overlay.show { display: block; }
</style>
</head>
<body>

<!-- ── Toast Container ────────────────────────────────── -->
<div class="toast-container" id="toastContainer"></div>

<!-- ── Edit Modal ─────────────────────────────────────── -->
<div class="modal-overlay hidden" id="editModal">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-head">
      <span class="modal-title" id="modalTitle">Edit Task</span>
      <button class="modal-close" onclick="closeModal()" aria-label="Close">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="editId">
      <div class="modal-field">
        <label>Task Title *</label>
        <input type="text" class="modal-input" id="editTitle" placeholder="What needs to be done?">
      </div>
      <div class="modal-field">
        <label>Description</label>
        <textarea class="modal-textarea" id="editDesc" placeholder="Add more details…"></textarea>
      </div>
      <div class="modal-row">
        <div class="modal-field">
          <label>Priority</label>
          <select class="modal-select" id="editPriority">
            <option value="low">🔵 Low</option>
            <option value="medium" selected>🟡 Medium</option>
            <option value="high">🔴 High</option>
          </select>
        </div>
        <div class="modal-field">
          <label>Due Date</label>
          <input type="date" class="modal-date" id="editDue">
        </div>
      </div>
      <div class="modal-field">
        <label>Status</label>
        <select class="modal-select" id="editStatus">
          <option value="pending">⏳ Pending</option>
          <option value="completed">✅ Completed</option>
        </select>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn-ghost" onclick="closeModal()">Cancel</button>
      <button class="btn-accent" onclick="saveEdit()">
        <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Save Changes
      </button>
    </div>
  </div>
</div>

<!-- ── Sidebar Overlay (mobile) ───────────────────────── -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ── App Shell ──────────────────────────────────────── -->
<div class="app">

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-head">
      <div class="logo">
        <div class="logo-icon">
          <svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </div>
        Taskly
      </div>
    </div>

    <!-- Stats -->
    <div class="sidebar-stats">
      <div class="stat-chip">
        <div class="num" id="statTotal"><?= $stats['total'] ?></div>
        <div class="lbl">Total</div>
      </div>
      <div class="stat-chip done">
        <div class="num" id="statDone"><?= $stats['completed'] ?></div>
        <div class="lbl">Done</div>
      </div>
      <div class="stat-chip">
        <div class="num" id="statPending"><?= $stats['pending'] ?></div>
        <div class="lbl">Pending</div>
      </div>
      <div class="stat-chip">
        <div class="num" id="statHigh"><?= $stats['high'] ?></div>
        <div class="lbl">High ⚡</div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
      <div class="nav-label">View</div>
      <button class="nav-item active" data-filter="all" onclick="setFilter('all', this)">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        All Tasks
        <span class="badge" id="navBadgeAll"><?= $stats['total'] ?></span>
      </button>
      <button class="nav-item" data-filter="pending" onclick="setFilter('pending', this)">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Pending
        <span class="badge" id="navBadgePending"><?= $stats['pending'] ?></span>
      </button>
      <button class="nav-item" data-filter="completed" onclick="setFilter('completed', this)">
        <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Completed
        <span class="badge" id="navBadgeDone"><?= $stats['completed'] ?></span>
      </button>

      <div class="nav-label">Priority</div>
      <button class="nav-item" data-filter="all" data-priority="high" onclick="setPriorityFilter('high', this)">
        <span class="priority-dot high"></span>
        High Priority
        <span class="badge" id="navBadgeHigh"><?= $stats['high'] ?></span>
      </button>
      <button class="nav-item" data-filter="all" data-priority="medium" onclick="setPriorityFilter('medium', this)">
        <span class="priority-dot medium"></span>
        Medium
        <span class="badge" id="navBadgeMedium"><?= $stats['medium'] ?></span>
      </button>
      <button class="nav-item" data-filter="all" data-priority="low" onclick="setPriorityFilter('low', this)">
        <span class="priority-dot low"></span>
        Low
        <span class="badge" id="navBadgeLow"><?= $stats['low'] ?></span>
      </button>
    </nav>

    <!-- User foot -->
    <div class="sidebar-foot">
      <div class="user-block">
        <div class="avatar"><?= strtoupper(substr(e($user['name']), 0, 1)) ?></div>
        <div>
          <div class="user-name"><?= e($user['name']) ?></div>
          <div class="user-email"><?= e($user['email']) ?></div>
        </div>
        <a href="logout.php" class="logout-btn" title="Sign out">
          <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </a>
      </div>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main">

    <!-- Topbar -->
    <header class="topbar">
      <button class="hamburger" onclick="toggleSidebar()" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>

      <div class="topbar-title" id="pageTitle">All Tasks</div>

      <div class="search-wrap">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" class="search-input" id="searchInput" placeholder="Search tasks…" oninput="handleSearch(this.value)">
      </div>

      <div class="topbar-actions">
        <button class="theme-btn" onclick="toggleTheme()" id="themeBtn" title="Toggle theme">🌙</button>
      </div>
    </header>

    <!-- Content -->
    <div class="content">

      <!-- Add Task Card -->
      <div class="add-task-card" id="addTaskCard">
        <div class="add-task-top">
          <input type="text" class="task-title-input" id="newTaskTitle"
                 placeholder="✚  Add a new task…"
                 onkeydown="if(event.key==='Enter')addTask()">
          <button class="add-expand-btn" onclick="toggleExtras()" id="expandBtn">
            <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            Details
          </button>
        </div>

        <div class="add-task-extras" id="taskExtras">
          <div class="add-task-extras-inner">
            <textarea class="task-desc-input" id="newTaskDesc" placeholder="Add a description…" rows="3"></textarea>
            <div class="form-field-wrap">
              <label>Priority</label>
              <select id="newTaskPriority">
                <option value="low">🔵 Low</option>
                <option value="medium" selected>🟡 Medium</option>
                <option value="high">🔴 High</option>
              </select>
            </div>
            <div class="form-field-wrap">
              <label>Due Date</label>
              <input type="date" class="date-input" id="newTaskDue">
            </div>
            <div class="add-task-footer" style="grid-column:1/-1">
              <button class="btn-ghost" onclick="resetForm()">Clear</button>
              <button class="btn-accent" onclick="addTask()" id="addBtn">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Task
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="filter-bar">
        <span class="filter-bar-label">Filter:</span>
        <button class="filter-btn active" data-filter="all" onclick="setFilter('all', this)">All</button>
        <button class="filter-btn" data-filter="pending" onclick="setFilter('pending', this)">Pending</button>
        <button class="filter-btn" data-filter="completed" onclick="setFilter('completed', this)">Completed</button>
      </div>

      <!-- Task List -->
      <div id="task-list">
        <div class="spinner"></div>
      </div>

    </div><!-- /content -->
  </main>
</div><!-- /app -->

<script>
/* ════════════════════════════════════════════════════
   TASKLY – Frontend JS
   ════════════════════════════════════════════════════ */

const CSRF = <?= json_encode($csrf) ?>;
let currentFilter   = 'all';
let currentPriority = null;
let searchQuery     = '';
let searchTimer     = null;
let dragSrc         = null;

// ── API ──────────────────────────────────────────────
async function api(action, body = null, method = 'GET') {
  const opts = {
    method,
    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF }
  };
  if (body) opts.body = JSON.stringify({ ...body, csrf_token: CSRF });
  const res  = await fetch(`api/tasks.php?action=${action}`, opts);
  return res.json();
}

// ── Tasks ─────────────────────────────────────────────
async function loadTasks() {
  const list = document.getElementById('task-list');
  list.innerHTML = '<div class="spinner"></div>';

  let url = `api/tasks.php?action=list&filter=${currentFilter}`;
  if (searchQuery) url += `&search=${encodeURIComponent(searchQuery)}`;

  const res = await fetch(url);
  const data = await res.json();

  if (!data.success) { showToast('Failed to load tasks', 'error'); return; }

  let tasks = data.tasks;
  if (currentPriority) tasks = tasks.filter(t => t.priority === currentPriority);

  renderTasks(tasks);
  await refreshStats();
}

function renderTasks(tasks) {
  const list = document.getElementById('task-list');
  if (!tasks.length) {
    list.innerHTML = `
      <div class="empty-state">
        <div class="empty-icon">
          <svg viewBox="0 0 24 24"><rect x="9" y="2" width="6" height="4" rx="1"/><path d="M18 4h1a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
        </div>
        <h3>No tasks yet</h3>
        <p>Add your first task above to get started!</p>
      </div>`;
    return;
  }

  list.innerHTML = tasks.map((t, i) => taskCard(t, i)).join('');

  // Bind drag events
  list.querySelectorAll('.task-card').forEach(card => {
    card.addEventListener('dragstart', onDragStart);
    card.addEventListener('dragover',  onDragOver);
    card.addEventListener('dragleave', onDragLeave);
    card.addEventListener('drop',      onDrop);
    card.addEventListener('dragend',   onDragEnd);
  });
}

function taskCard(t, idx) {
  const isComplete = t.status === 'completed';
  const prioLabel  = t.priority.charAt(0).toUpperCase() + t.priority.slice(1);
  const prioIcon   = t.priority === 'high' ? '🔴' : t.priority === 'medium' ? '🟡' : '🔵';

  let dueMeta = '';
  if (t.due_date) {
    const due = new Date(t.due_date + 'T00:00:00');
    const now = new Date(); now.setHours(0,0,0,0);
    const overdue = !isComplete && due < now;
    const label = due.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    dueMeta = `<span class="meta-chip chip-date ${overdue ? 'overdue' : ''}">
      <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      ${overdue ? '⚠ ' : ''}${escHtml(label)}
    </span>`;
  }

  const createdDate = new Date(t.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });

  return `
  <div class="task-card priority-${escHtml(t.priority)} ${isComplete ? 'completed' : ''}"
       data-id="${t.id}" draggable="true"
       style="animation-delay:${idx * 0.04}s">

    <div class="drag-handle" title="Drag to reorder">
      <svg viewBox="0 0 24 24" width="14" height="14"><circle cx="9" cy="5" r="1.5"/><circle cx="15" cy="5" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="19" r="1.5"/></svg>
    </div>

    <button class="task-check ${isComplete ? 'checked' : ''}"
            onclick="toggleTask(${t.id})" title="${isComplete ? 'Mark pending' : 'Mark complete'}">
      <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </button>

    <div class="task-body">
      <div class="task-title">${escHtml(t.title)}</div>
      ${t.description ? `<div class="task-desc">${escHtml(t.description)}</div>` : ''}
      <div class="task-meta">
        <span class="meta-chip chip-priority-${escHtml(t.priority)}">${prioIcon} ${prioLabel}</span>
        ${dueMeta}
        ${isComplete ? `<span class="meta-chip chip-status-completed">✓ Completed</span>` : ''}
        <span class="meta-chip chip-date">Added ${escHtml(createdDate)}</span>
      </div>
    </div>

    <div class="task-actions">
      <button class="act-btn" onclick="openEdit(${t.id})" title="Edit">
        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      </button>
      <button class="act-btn delete" onclick="deleteTask(${t.id})" title="Delete">
        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
      </button>
    </div>
  </div>`;
}

// ── Add Task ──────────────────────────────────────────
async function addTask() {
  const title = document.getElementById('newTaskTitle').value.trim();
  if (!title) { document.getElementById('newTaskTitle').focus(); return; }

  const btn = document.getElementById('addBtn');
  btn.disabled = true; btn.textContent = 'Adding…';

  const data = await api('create', {
    title,
    description: document.getElementById('newTaskDesc').value,
    priority:    document.getElementById('newTaskPriority').value,
    due_date:    document.getElementById('newTaskDue').value,
  }, 'POST');

  btn.disabled = false;
  btn.innerHTML = `<svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Add Task`;

  if (data.success) {
    resetForm();
    showToast('Task added!', 'success');
    loadTasks();
  } else {
    showToast(data.message || 'Error', 'error');
  }
}

function resetForm() {
  document.getElementById('newTaskTitle').value   = '';
  document.getElementById('newTaskDesc').value    = '';
  document.getElementById('newTaskPriority').value = 'medium';
  document.getElementById('newTaskDue').value     = '';
  document.getElementById('taskExtras').classList.remove('open');
  document.getElementById('expandBtn').innerHTML = `<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>Details`;
}

function toggleExtras() {
  const el = document.getElementById('taskExtras');
  const open = el.classList.toggle('open');
  document.getElementById('expandBtn').innerHTML = open
    ? `<svg viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg>Hide`
    : `<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>Details`;
  if (open) document.getElementById('newTaskDesc').focus();
}

// ── Toggle ────────────────────────────────────────────
async function toggleTask(id) {
  const data = await api('toggle', { id }, 'POST');
  if (data.success) {
    const card = document.querySelector(`.task-card[data-id="${id}"]`);
    if (card) {
      const isCompleted = data.status === 'completed';
      card.classList.toggle('completed', isCompleted);
      card.querySelector('.task-check').classList.toggle('checked', isCompleted);
      const checkDisplay = card.querySelector('.task-check svg');
      if (checkDisplay) checkDisplay.style.display = isCompleted ? 'block' : 'none';
      showToast(isCompleted ? '✓ Task completed!' : 'Marked as pending', 'success');
    }
    refreshStats();
    // Reload if filter would hide it
    if (currentFilter !== 'all') loadTasks();
  }
}

// ── Delete ────────────────────────────────────────────
async function deleteTask(id) {
  if (!confirm('Delete this task? This cannot be undone.')) return;
  const data = await api('delete', { id }, 'POST');
  if (data.success) {
    const card = document.querySelector(`.task-card[data-id="${id}"]`);
    if (card) {
      card.style.transition = 'opacity .2s, transform .2s';
      card.style.opacity    = '0';
      card.style.transform  = 'translateX(20px)';
      setTimeout(() => { card.remove(); refreshStats(); }, 200);
    }
    showToast('Task deleted', 'error');
    refreshStats();
  }
}

// ── Edit Modal ────────────────────────────────────────
const taskCache = {};

async function openEdit(id) {
  // Fetch task via list search
  const res  = await fetch(`api/tasks.php?action=list&filter=all`);
  const data = await res.json();
  const task = data.tasks?.find(t => t.id == id);
  if (!task) return;

  document.getElementById('editId').value          = task.id;
  document.getElementById('editTitle').value        = task.title;
  document.getElementById('editDesc').value         = task.description || '';
  document.getElementById('editPriority').value     = task.priority;
  document.getElementById('editDue').value          = task.due_date || '';
  document.getElementById('editStatus').value       = task.status;
  document.getElementById('editModal').classList.remove('hidden');
  document.getElementById('editTitle').focus();
}

function closeModal() {
  document.getElementById('editModal').classList.add('hidden');
}

async function saveEdit() {
  const id = document.getElementById('editId').value;
  const data = await api('update', {
    id:          id,
    title:       document.getElementById('editTitle').value,
    description: document.getElementById('editDesc').value,
    priority:    document.getElementById('editPriority').value,
    due_date:    document.getElementById('editDue').value,
    status:      document.getElementById('editStatus').value,
  }, 'POST');

  if (data.success) {
    closeModal();
    showToast('Task saved!', 'success');
    loadTasks();
  } else {
    showToast(data.message || 'Error saving', 'error');
  }
}

// Close modal on overlay click
document.getElementById('editModal').addEventListener('click', e => {
  if (e.target === e.currentTarget) closeModal();
});

// Esc key
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeModal();
  if (e.key === 'Enter' && e.ctrlKey) addTask();
});

// ── Filters ───────────────────────────────────────────
function setFilter(filter, btn) {
  currentFilter   = filter;
  currentPriority = null;

  document.querySelectorAll('.filter-btn, .nav-item').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  // Also sync filter bar
  document.querySelectorAll(`.filter-btn[data-filter="${filter}"]`).forEach(b => b.classList.add('active'));
  document.querySelectorAll(`.nav-item[data-filter="${filter}"]:not([data-priority])`).forEach(b => b.classList.add('active'));

  const titles = { all: 'All Tasks', pending: 'Pending Tasks', completed: 'Completed Tasks' };
  document.getElementById('pageTitle').textContent = titles[filter] || 'Tasks';

  loadTasks();
}

function setPriorityFilter(priority, btn) {
  currentFilter   = 'all';
  currentPriority = priority;

  document.querySelectorAll('.filter-btn, .nav-item').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  document.getElementById('pageTitle').textContent = priority.charAt(0).toUpperCase() + priority.slice(1) + ' Priority';
  loadTasks();
}

// ── Search ────────────────────────────────────────────
function handleSearch(val) {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    searchQuery = val.trim();
    loadTasks();
  }, 350);
}

// ── Stats ─────────────────────────────────────────────
async function refreshStats() {
  const res  = await fetch('api/tasks.php?action=stats');
  const data = await res.json();
  if (!data.success) return;
  const s = data.stats;
  document.getElementById('statTotal').textContent    = s.total;
  document.getElementById('statDone').textContent     = s.completed;
  document.getElementById('statPending').textContent  = s.pending;
  document.getElementById('statHigh').textContent     = s.high;
  document.getElementById('navBadgeAll').textContent     = s.total;
  document.getElementById('navBadgePending').textContent = s.pending;
  document.getElementById('navBadgeDone').textContent    = s.completed;
  document.getElementById('navBadgeHigh').textContent    = s.high;
  document.getElementById('navBadgeMedium').textContent  = s.medium;
  document.getElementById('navBadgeLow').textContent     = s.low;
}

// ── Drag & Drop Reorder ───────────────────────────────
function onDragStart(e) {
  dragSrc = this;
  this.classList.add('dragging');
  e.dataTransfer.effectAllowed = 'move';
  e.dataTransfer.setData('text/plain', this.dataset.id);
}
function onDragOver(e) {
  e.preventDefault();
  e.dataTransfer.dropEffect = 'move';
  this.classList.add('drag-over');
}
function onDragLeave() { this.classList.remove('drag-over'); }
function onDrop(e) {
  e.preventDefault();
  this.classList.remove('drag-over');
  if (dragSrc === this) return;
  const list = document.getElementById('task-list');
  const cards = [...list.querySelectorAll('.task-card')];
  const fromIdx = cards.indexOf(dragSrc);
  const toIdx   = cards.indexOf(this);
  if (fromIdx < toIdx) this.after(dragSrc);
  else this.before(dragSrc);
  saveOrder();
}
function onDragEnd() {
  this.classList.remove('dragging');
  document.querySelectorAll('.task-card').forEach(c => c.classList.remove('drag-over'));
}
async function saveOrder() {
  const ids = [...document.querySelectorAll('.task-card')].map(c => parseInt(c.dataset.id));
  await api('reorder', { ids }, 'POST');
}

// ── Toast ─────────────────────────────────────────────
function showToast(msg, type = '') {
  const el = document.createElement('div');
  el.className = `toast ${type}`;
  el.textContent = msg;
  document.getElementById('toastContainer').appendChild(el);
  setTimeout(() => {
    el.classList.add('exit');
    setTimeout(() => el.remove(), 350);
  }, 3000);
}

// ── Theme ─────────────────────────────────────────────
function toggleTheme() {
  const html  = document.documentElement;
  const isDark = html.getAttribute('data-theme') === 'dark';
  html.setAttribute('data-theme', isDark ? 'light' : 'dark');
  document.getElementById('themeBtn').textContent = isDark ? '🌙' : '☀️';
  localStorage.setItem('theme', isDark ? 'light' : 'dark');
}

// ── Sidebar (mobile) ──────────────────────────────────
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebarOverlay').classList.toggle('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebarOverlay').classList.remove('show');
}

// ── XSS safe output ──────────────────────────────────
function escHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

// ── Init ──────────────────────────────────────────────
(function init() {
  // Restore theme
  const saved = localStorage.getItem('theme') || 'light';
  document.documentElement.setAttribute('data-theme', saved);
  document.getElementById('themeBtn').textContent = saved === 'dark' ? '☀️' : '🌙';

  loadTasks();

  // Auto-expand add form when typing
  document.getElementById('newTaskTitle').addEventListener('focus', () => {
    document.getElementById('taskExtras').classList.add('open');
    document.getElementById('expandBtn').innerHTML = `<svg viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg>Hide`;
  });
})();
</script>
</body>
</html>
