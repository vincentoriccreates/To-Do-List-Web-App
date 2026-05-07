# Taskly — PHP To-Do App

A professional, full-featured task management application built with PHP, MySQL, and vanilla JavaScript.

---

## 🚀 Quick Start

### Requirements
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.4+
- Apache/Nginx with mod_rewrite (or PHP built-in server)

---

## 📦 Installation

### 1. Copy files to your server

```bash
# Apache (XAMPP/WAMP/LAMP)
cp -r todo-app/ /var/www/html/todo-app/

# Or for XAMPP on Windows:
# Copy folder to C:\xampp\htdocs\todo-app\
```

### 2. Create the database

**Option A — MySQL CLI:**
```bash
mysql -u root -p < setup.sql
```

**Option B — phpMyAdmin:**
1. Open phpMyAdmin
2. Click "Import"
3. Select `setup.sql`
4. Click "Go"

**Option C — Run setup script:**
Visit `http://localhost/todo-app/install.php` in your browser

### 3. Configure database credentials

Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'todo_app');
define('DB_USER', 'root');        // your MySQL username
define('DB_PASS', '');            // your MySQL password
```

### 4. Open in browser

```
http://localhost/todo-app/
```

---

## 📁 Folder Structure

```
todo-app/
├── api/
│   └── tasks.php          # AJAX API endpoints
├── includes/
│   ├── config.php         # App configuration
│   ├── database.php       # PDO connection (singleton)
│   ├── auth.php           # Auth helpers (login, register, CSRF)
│   └── tasks.php          # Task CRUD functions
├── index.php              # Main dashboard
├── login.php              # Login page
├── register.php           # Registration page
├── logout.php             # Logout handler
├── install.php            # One-click DB installer
├── setup.sql              # Database schema
└── README.md
```

---

## ✨ Features

| Feature | Details |
|---|---|
| Auth | Register, login, logout with bcrypt hashing |
| Tasks | Create, read, update, delete |
| Priorities | High / Medium / Low with color coding |
| Due Dates | With overdue highlighting |
| Status | Pending / Completed with strikethrough |
| Filters | All / Pending / Completed / By priority |
| Search | Live search with debounce |
| Stats | Total, completed, pending, high-priority counts |
| AJAX | All task operations without page reload |
| Drag & Drop | Reorder tasks by dragging |
| Dark Mode | Toggle with localStorage persistence |
| Responsive | Mobile-friendly sidebar + layout |
| Security | CSRF tokens, prepared statements, XSS protection |

---

## 🔒 Security

- **Password hashing**: bcrypt via `password_hash()` with cost factor 12
- **SQL injection**: All queries use PDO prepared statements
- **XSS prevention**: All output escaped with `htmlspecialchars()`
- **CSRF protection**: Token verified on every state-changing request
- **Session security**: `httponly`, `samesite=Lax`, session regeneration on login

---

## 🛠 Production Deployment

Before going live:

1. Set `error_reporting(0)` in `config.php`
2. Set `'secure' => true` in session cookie params (HTTPS)
3. Change `DB_PASS` to a strong password
4. Remove or password-protect `install.php`
5. Set correct file permissions: `chmod 750 includes/`

---

## 📋 Database Schema

### `users`
| Column | Type | Notes |
|---|---|---|
| id | INT UNSIGNED | Primary key |
| name | VARCHAR(100) | Display name |
| email | VARCHAR(255) | Unique |
| password_hash | VARCHAR(255) | bcrypt |
| created_at | TIMESTAMP | Auto |

### `tasks`
| Column | Type | Notes |
|---|---|---|
| id | INT UNSIGNED | Primary key |
| user_id | INT UNSIGNED | FK → users |
| title | VARCHAR(255) | Required |
| description | TEXT | Optional |
| due_date | DATE | Optional |
| priority | ENUM | low/medium/high |
| status | ENUM | pending/completed |
| sort_order | INT | Drag-drop order |
| created_at | TIMESTAMP | Auto |
| updated_at | TIMESTAMP | Auto |

---

## 📄 License

MIT — free to use, modify, and distribute.
