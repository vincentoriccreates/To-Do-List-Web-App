<<<<<<< HEAD
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
=======
# 📝 To-Do List Web App

A modern, professional **To-Do List Web Application** built with **PHP, MySQL, and JavaScript**.
Designed with a clean UI and practical features for managing daily tasks efficiently.

---

## 🚀 Features

### 🔐 Authentication

* User registration
* Secure login/logout
* Password hashing (`password_hash`)

### 📋 Task Management

* Add, edit, delete tasks
* Mark tasks as complete/incomplete
* Task ownership per user

### 🧩 Task Details

* Title
* Description (optional)
* Due date
* Priority (Low, Medium, High)
* Status (Pending / Completed)
* Created timestamp

---

## 🎨 UI / UX

* Clean and minimal dashboard layout
* Responsive design (mobile-friendly)
* Task list displayed as cards or list items
* Strikethrough style for completed tasks
* Icons for actions (edit, delete, complete)
* Smooth hover effects and transitions
* Modal or inline editing
* Empty state message: *"No tasks yet"*

---

## 🖌️ Design Style

* Modern SaaS-inspired interface
* Google Fonts (Poppins / Inter)
* Soft color palette (white, gray, blue/green accent)
* Rounded corners & soft shadows
* Consistent spacing and alignment
* Optional dark mode 🌙

---

## ⚙️ Tech Stack

* **Backend:** PHP (Core PHP)
* **Database:** MySQL
* **Frontend:** HTML, CSS, JavaScript
* **Optional:** AJAX for real-time updates
>>>>>>> 7b9451bd74d2a37ffa4fa0533abc01b0948c661f

---

## 🔒 Security

<<<<<<< HEAD
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
=======
* Password hashing using `password_hash()`
* Prepared statements (PDO/MySQLi)
* Protection against:

  * SQL Injection
  * Cross-Site Scripting (XSS)

---

## 📁 Project Structure

```bash
todo-app/
│── config.php
│── index.php
│── login.php
│── register.php
│── logout.php
│── add.php
│── update.php
│── delete.php
│── assets/
│   ├── css/
│   ├── js/
│   └── images/
```

---

## 🗄️ Database Setup

```sql
CREATE DATABASE todo_app;

USE todo_app;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    password VARCHAR(255)
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255),
    description TEXT,
    due_date DATE,
    priority ENUM('Low','Medium','High'),
    status ENUM('pending','done') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🛠️ Installation

1. Clone the repository:

```bash
git clone https://github.com/your-username/todo-app.git
```

2. Move the project to your local server (e.g., XAMPP `htdocs`)

3. Import the database:

* Open **phpMyAdmin**
* Create database: `todo_app`
* Run the SQL script above

4. Configure database connection in `config.php`

5. Start Apache & MySQL, then open:

```
http://localhost/todo-app
```

---

## ✨ Optional Enhancements

* Task filtering (All / Completed / Pending)
* Search functionality
* Drag-and-drop task sorting
* Dashboard statistics
* Notifications system
* Dark mode toggle

---

## 📸 Screenshots

> Add your screenshots here
> (Dashboard, Login Page, Task List, etc.)

---

## 🌐 Live Demo

> Add your deployed link here (if available)

---

## 🤝 Contributing

Contributions are welcome!
Feel free to fork this project and submit a pull request.
>>>>>>> 7b9451bd74d2a37ffa4fa0533abc01b0948c661f

---

## 📄 License

<<<<<<< HEAD
MIT — free to use, modify, and distribute.
=======
This project is open-source and available under the **MIT License**.

---

## 👨‍💻 Author

**Vincent Oric**

* WordPress Developer
* Graphic Designer
* Virtual Assistant

Feel free to connect or collaborate!
>>>>>>> 7b9451bd74d2a37ffa4fa0533abc01b0948c661f
