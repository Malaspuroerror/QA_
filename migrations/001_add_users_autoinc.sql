-- Migration: Make `users.id` an INT AUTO_INCREMENT PRIMARY KEY
-- Run this once in your MySQL/MariaDB environment (phpMyAdmin, CLI or a DB manager).

ALTER TABLE `users`
  MODIFY `id` INT NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`id`);

-- Note: if `id` already has a primary key or non-integer values, please back up the table first.
