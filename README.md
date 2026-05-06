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

---

## 🔒 Security

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

---

## 📄 License

This project is open-source and available under the **MIT License**.

---

## 👨‍💻 Author

**Vincent Oric**

* WordPress Developer
* Graphic Designer
* Virtual Assistant

Feel free to connect or collaborate!
