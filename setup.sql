-- ============================================================
--  Taskly – Database Setup Script
--  Run this once to create the database and tables
-- ============================================================

CREATE DATABASE IF NOT EXISTS `todo_app`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `todo_app`;

-- ── Users ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(100)    NOT NULL,
    `email`         VARCHAR(255)    NOT NULL UNIQUE,
    `password_hash` VARCHAR(255)    NOT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Tasks ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `tasks` (
    `id`          INT UNSIGNED                          NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED                          NOT NULL,
    `title`       VARCHAR(255)                          NOT NULL,
    `description` TEXT                                  NULL,
    `due_date`    DATE                                  NULL,
    `priority`    ENUM('low', 'medium', 'high')         NOT NULL DEFAULT 'medium',
    `status`      ENUM('pending', 'completed')          NOT NULL DEFAULT 'pending',
    `sort_order`  INT UNSIGNED                          NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP                             NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_tasks_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_status` (`user_id`, `status`),
    INDEX `idx_user_priority` (`user_id`, `priority`),
    INDEX `idx_due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Demo user (password: Password123) ──────────────────────
-- INSERT INTO `users` (`name`, `email`, `password_hash`) VALUES
-- ('Demo User', 'demo@taskly.app', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
