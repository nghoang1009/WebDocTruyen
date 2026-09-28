-- Database Schema for WebDocTruyen
-- Character Set: utf8mb4, Collation: utf8mb4_unicode_ci

CREATE DATABASE IF NOT EXISTS `webdoctruyen` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `webdoctruyen`;

-- 1. Roles table
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `description` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL DEFAULT 2,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `avatar` VARCHAR(255) DEFAULT 'default-avatar.png',
    `status` ENUM('active', 'banned') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL,
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Authors table
CREATE TABLE IF NOT EXISTS `authors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `bio` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Categories table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Stories table
CREATE TABLE IF NOT EXISTS `stories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `author_id` INT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(280) NOT NULL UNIQUE,
    `description` LONGTEXT NOT NULL,
    `cover_image` VARCHAR(255) DEFAULT 'default-cover.jpg',
    `status` ENUM('updating', 'completed', 'paused') DEFAULT 'updating',
    `views_count` INT UNSIGNED DEFAULT 0,
    `rating_avg` DECIMAL(3, 2) DEFAULT 0.00,
    `rating_count` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_stories_author` FOREIGN KEY (`author_id`) REFERENCES `authors`(`id`) ON DELETE SET NULL,
    INDEX `idx_stories_views` (`views_count`),
    INDEX `idx_stories_rating` (`rating_avg`),
    INDEX `idx_stories_status` (`status`),
    INDEX `idx_stories_updated` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Story Categories junction table
CREATE TABLE IF NOT EXISTS `story_categories` (
    `story_id` INT NOT NULL,
    `category_id` INT NOT NULL,
    PRIMARY KEY (`story_id`, `category_id`),
    CONSTRAINT `fk_sc_story` FOREIGN KEY (`story_id`) REFERENCES `stories`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sc_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Chapters table
CREATE TABLE IF NOT EXISTS `chapters` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `story_id` INT NOT NULL,
    `chapter_number` FLOAT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(280) NOT NULL,
    `content` LONGTEXT NOT NULL,
    `views_count` INT UNSIGNED DEFAULT 0,
    `status` ENUM('published', 'draft') DEFAULT 'published',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_story_chapter_num` (`story_id`, `chapter_number`),
    UNIQUE KEY `uk_story_chapter_slug` (`story_id`, `slug`),
    CONSTRAINT `fk_chapters_story` FOREIGN KEY (`story_id`) REFERENCES `stories`(`id`) ON DELETE CASCADE,
    INDEX `idx_chapter_order` (`story_id`, `chapter_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Reading History table
CREATE TABLE IF NOT EXISTS `reading_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `story_id` INT NOT NULL,
    `chapter_id` INT NOT NULL,
    `progress_percent` INT DEFAULT 0,
    `last_read_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_story_history` (`user_id`, `story_id`),
    CONSTRAINT `fk_history_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_history_story` FOREIGN KEY (`story_id`) REFERENCES `stories`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_history_chapter` FOREIGN KEY (`chapter_id`) REFERENCES `chapters`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Follows table
CREATE TABLE IF NOT EXISTS `follows` (
    `user_id` INT NOT NULL,
    `story_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `story_id`),
    CONSTRAINT `fk_follows_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_follows_story` FOREIGN KEY (`story_id`) REFERENCES `stories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Favorites table
CREATE TABLE IF NOT EXISTS `favorites` (
    `user_id` INT NOT NULL,
    `story_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `story_id`),
    CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_favorites_story` FOREIGN KEY (`story_id`) REFERENCES `stories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Ratings table
CREATE TABLE IF NOT EXISTS `ratings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `story_id` INT NOT NULL,
    `rating` TINYINT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_story_rating` (`user_id`, `story_id`),
    CONSTRAINT `fk_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ratings_story` FOREIGN KEY (`story_id`) REFERENCES `stories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Comments table
CREATE TABLE IF NOT EXISTS `comments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `story_id` INT NOT NULL,
    `chapter_id` INT NULL,
    `parent_id` INT NULL,
    `content` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_comments_story` FOREIGN KEY (`story_id`) REFERENCES `stories`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_comments_chapter` FOREIGN KEY (`chapter_id`) REFERENCES `chapters`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) REFERENCES `comments`(`id`) ON DELETE CASCADE,
    INDEX `idx_comments_story` (`story_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Views Log table
CREATE TABLE IF NOT EXISTS `views_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `story_id` INT NOT NULL,
    `chapter_id` INT NULL,
    `view_date` DATE NOT NULL,
    `views_count` INT UNSIGNED DEFAULT 1,
    UNIQUE KEY `uk_story_chapter_date` (`story_id`, `chapter_id`, `view_date`),
    CONSTRAINT `fk_views_story` FOREIGN KEY (`story_id`) REFERENCES `stories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
