-- =============================================================================
-- Migration: Add optional avatar column to users table
-- Run this once to enable profile picture uploads (e.g. in phpMyAdmin or mysql)
-- =============================================================================

USE `php_oop`;

ALTER TABLE `users`
ADD COLUMN `avatar` VARCHAR(255) NULL DEFAULT NULL
COMMENT 'Stored filename in uploads/avatars/ (e.g. 42_abc123.jpg)' AFTER `name`;
