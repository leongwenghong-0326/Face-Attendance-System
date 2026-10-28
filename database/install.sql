-- Facial Recognition Student Attendance Management System
-- Database Installation Script
-- Target: MySQL 5.7+ / MariaDB 10.3+
-- Engine: InnoDB, Charset: utf8mb4, Collation: utf8mb4_unicode_ci

CREATE DATABASE IF NOT EXISTS `face_attendance_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `face_attendance_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users table (Admin, Teacher, Student)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(60) NOT NULL UNIQUE,
    `email` VARCHAR(120) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'teacher', 'student') NOT NULL DEFAULT 'student',
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Classes table
DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `class_name` VARCHAR(100) NOT NULL,
    `class_code` VARCHAR(30) NOT NULL UNIQUE,
    `start_time` TIME NOT NULL DEFAULT '08:00:00',
    `late_grace_minutes` INT UNSIGNED NOT NULL DEFAULT 15,
    `rest_start_time` TIME NOT NULL DEFAULT '12:00:00',
    `rest_end_time` TIME NOT NULL DEFAULT '13:00:00',
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_classes_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Subjects table
DROP TABLE IF EXISTS `subjects`;
CREATE TABLE `subjects` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `subject_code` VARCHAR(30) NOT NULL UNIQUE,
    `subject_name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_subjects_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Teachers table
DROP TABLE IF EXISTS `teachers`;
CREATE TABLE `teachers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL UNIQUE,
    `teacher_code` VARCHAR(30) NOT NULL UNIQUE,
    `full_name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(120) NOT NULL,
    `phone` VARCHAR(30) NULL,
    `gender` ENUM('male', 'female', 'other') DEFAULT 'other',
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_teachers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_teachers_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Students table
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL UNIQUE,
    `student_code` VARCHAR(30) NOT NULL UNIQUE,
    `full_name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(120) NOT NULL,
    `phone` VARCHAR(30) NULL,
    `gender` ENUM('male', 'female', 'other') DEFAULT 'other',
    `date_of_birth` DATE NULL,
    `class_id` INT UNSIGNED NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `is_face_registered` TINYINT(1) NOT NULL DEFAULT 0,
    `face_version` INT UNSIGNED NOT NULL DEFAULT 0,
    `face_preview_image` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_students_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
    INDEX `idx_students_class` (`class_id`),
    INDEX `idx_students_face_reg` (`is_face_registered`),
    INDEX `idx_students_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Class-Subjects table (Many-to-Many)
DROP TABLE IF EXISTS `class_subjects`;
CREATE TABLE `class_subjects` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `class_id` INT UNSIGNED NOT NULL,
    `subject_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_class_subject` (`class_id`, `subject_id`),
    CONSTRAINT `fk_cs_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cs_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Teacher Assignments table
DROP TABLE IF EXISTS `teacher_assignments`;
CREATE TABLE `teacher_assignments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `teacher_id` INT UNSIGNED NOT NULL,
    `class_id` INT UNSIGNED NOT NULL,
    `subject_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_teacher_class_subject` (`teacher_id`, `class_id`, `subject_id`),
    CONSTRAINT `fk_ta_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ta_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ta_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Face Registrations History table
DROP TABLE IF EXISTS `face_registrations`;
CREATE TABLE `face_registrations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT UNSIGNED NOT NULL,
    `version` INT UNSIGNED NOT NULL DEFAULT 1,
    `sample_count` INT UNSIGNED NOT NULL DEFAULT 3,
    `preview_image` VARCHAR(255) NULL,
    `status` ENUM('active', 'superseded') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_fr_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    INDEX `idx_fr_student_status` (`student_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Face Descriptors table
DROP TABLE IF EXISTS `face_descriptors`;
CREATE TABLE `face_descriptors` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `registration_id` INT UNSIGNED NOT NULL,
    `student_id` INT UNSIGNED NOT NULL,
    `descriptor` LONGTEXT NOT NULL COMMENT 'JSON array of 128 floats',
    `sample_index` INT UNSIGNED NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_fd_registration` FOREIGN KEY (`registration_id`) REFERENCES `face_registrations` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fd_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    INDEX `idx_fd_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Attendance table
DROP TABLE IF EXISTS `attendance`;
CREATE TABLE `attendance` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT UNSIGNED NOT NULL,
    `class_id` INT UNSIGNED NOT NULL,
    `attendance_date` DATE NOT NULL,
    `check_in_time` TIME NULL,
    `check_in_confidence` DECIMAL(6,4) NULL,
    `check_in_photo` VARCHAR(255) NULL,
    `check_out_time` TIME NULL,
    `check_out_confidence` DECIMAL(6,4) NULL,
    `check_out_photo` VARCHAR(255) NULL,
    `status` ENUM('checked_in', 'checked_out', 'late', 'absent', 'on_leave') NOT NULL DEFAULT 'checked_in',
    `notes` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_attendance_student_date` (`student_id`, `attendance_date`),
    CONSTRAINT `fk_att_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_att_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
    INDEX `idx_att_date` (`attendance_date`),
    INDEX `idx_att_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Rest Attendance table
DROP TABLE IF EXISTS `rest_attendance`;
CREATE TABLE `rest_attendance` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT UNSIGNED NOT NULL,
    `class_id` INT UNSIGNED NOT NULL,
    `rest_date` DATE NOT NULL,
    `rest_in_time` TIME NULL,
    `rest_in_confidence` DECIMAL(6,4) NULL,
    `rest_in_photo` VARCHAR(255) NULL,
    `rest_out_time` TIME NULL,
    `rest_out_confidence` DECIMAL(6,4) NULL,
    `rest_out_photo` VARCHAR(255) NULL,
    `status` ENUM('rest_in', 'rest_out') NOT NULL DEFAULT 'rest_in',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_rest_student_date` (`student_id`, `rest_date`),
    CONSTRAINT `fk_ra_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ra_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
    INDEX `idx_ra_date` (`rest_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Student Leave Applications table
DROP TABLE IF EXISTS `student_leave_applications`;
CREATE TABLE `student_leave_applications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT UNSIGNED NOT NULL,
    `leave_date` DATE NOT NULL,
    `reason` TEXT NOT NULL,
    `document_path` VARCHAR(255) NULL,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `review_note` TEXT NULL,
    `reviewed_by` INT UNSIGNED NULL,
    `reviewed_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sla_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sla_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_sla_date` (`leave_date`),
    INDEX `idx_sla_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Audit Logs table
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL,
    `student_id` INT UNSIGNED NULL,
    `action` VARCHAR(80) NOT NULL,
    `confidence_score` DECIMAL(6,4) NULL,
    `registration_version` INT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `metadata` JSON NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_al_action` (`action`),
    INDEX `idx_al_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Settings table
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(60) NOT NULL UNIQUE,
    `setting_value` TEXT NOT NULL,
    `setting_group` VARCHAR(40) NOT NULL DEFAULT 'general',
    `description` VARCHAR(255) NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================
-- DEFAULT SETTINGS
-- ==========================================
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `description`) VALUES
('min_face_size', '80', 'face', 'Minimum face bounding box width/height in pixels'),
('max_face_size', '440', 'face', 'Maximum face bounding box width/height in pixels'),
('min_brightness', '40', 'face', 'Minimum average lighting brightness (0-255)'),
('max_brightness', '225', 'face', 'Maximum average lighting brightness (0-255)'),
('min_sharpness', '5', 'face', 'Minimum image sharpness edge gradient score'),
('detection_confidence', '0.50', 'face', 'Detector confidence threshold for face detection'),
('match_threshold', '0.55', 'face', 'Euclidean distance matching threshold (lower is stricter)'),
('min_confidence', '0.45', 'face', 'Minimum confidence percentage required for check-in/out'),
('stable_frames', '2', 'face', 'Number of consecutive stable frames required before capture'),
('duplicate_scan_interval', '300', 'face', 'Seconds to wait before allowing another scan from same student'),
('registration_samples', '3', 'face', 'Number of face samples captured during face registration'),
('institution_name', 'Apex Institute of Advanced Technology', 'general', 'Name of the educational institution'),
('system_timezone', 'Asia/Kuala_Lumpur', 'general', 'System default timezone');

-- ==========================================
-- SEED DATA (Demo Accounts, Classes, Subjects)
-- Password for all demo accounts: password123
-- ==========================================
-- Admin user
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `status`) VALUES
(1, 'admin', 'admin@apex.edu', '$2y$10$RyF5j1j6lDQomJDf/fuWsesfMd5waVOMuv0Kf4LKAeNRk/yuUFqJq', 'admin', 'active');

-- Teacher users
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `status`) VALUES
(2, 'teacher1', 'alice@apex.edu', '$2y$10$RyF5j1j6lDQomJDf/fuWsesfMd5waVOMuv0Kf4LKAeNRk/yuUFqJq', 'teacher', 'active'),
(3, 'teacher2', 'bob@apex.edu', '$2y$10$RyF5j1j6lDQomJDf/fuWsesfMd5waVOMuv0Kf4LKAeNRk/yuUFqJq', 'teacher', 'active');

-- Student users
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `status`) VALUES
(4, 'student1', 'john@apex.edu', '$2y$10$RyF5j1j6lDQomJDf/fuWsesfMd5waVOMuv0Kf4LKAeNRk/yuUFqJq', 'student', 'active'),
(5, 'student2', 'jane@apex.edu', '$2y$10$RyF5j1j6lDQomJDf/fuWsesfMd5waVOMuv0Kf4LKAeNRk/yuUFqJq', 'student', 'active'),
(6, 'student3', 'alex@apex.edu', '$2y$10$RyF5j1j6lDQomJDf/fuWsesfMd5waVOMuv0Kf4LKAeNRk/yuUFqJq', 'student', 'active');

-- Teachers table
INSERT INTO `teachers` (`id`, `user_id`, `teacher_code`, `full_name`, `email`, `phone`, `gender`, `status`) VALUES
(1, 2, 'TCH-001', 'Dr. Alice Johnson', 'alice@apex.edu', '+60 12-345 6789', 'female', 'active'),
(2, 3, 'TCH-002', 'Prof. Bob Smith', 'bob@apex.edu', '+60 13-987 6543', 'male', 'active');

-- Classes
INSERT INTO `classes` (`id`, `class_name`, `class_code`, `start_time`, `late_grace_minutes`, `rest_start_time`, `rest_end_time`, `status`) VALUES
(1, 'Computer Science Year 1 A', 'CS-Y1A', '08:00:00', 15, '12:00:00', '13:00:00', 'active'),
(2, 'Software Engineering Year 2 B', 'SE-Y2B', '08:30:00', 10, '12:30:00', '13:30:00', 'active'),
(3, 'Data Science Year 3 A', 'DS-Y3A', '09:00:00', 15, '12:00:00', '13:00:00', 'active');

-- Subjects
INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `description`, `status`) VALUES
(1, 'CS101', 'Introduction to Programming', 'Foundations of programming using Python and C++', 'active'),
(2, 'CS102', 'Database Management Systems', 'Relational database architecture, SQL, and indexing', 'active'),
(3, 'SE201', 'Web Application Development', 'Full-stack modern web architecture and security', 'active'),
(4, 'DS301', 'Machine Learning & AI', 'Neural networks, computer vision, and facial analysis', 'active');

-- Class-Subject assignments
INSERT INTO `class_subjects` (`class_id`, `subject_id`) VALUES
(1, 1),
(1, 2),
(2, 3),
(3, 4);

-- Teacher assignments
INSERT INTO `teacher_assignments` (`teacher_id`, `class_id`, `subject_id`) VALUES
(1, 1, 1),
(1, 2, 3),
(2, 1, 2),
(2, 3, 4);

-- Students
INSERT INTO `students` (`id`, `user_id`, `student_code`, `full_name`, `email`, `phone`, `gender`, `date_of_birth`, `class_id`, `status`, `is_face_registered`, `face_version`, `face_preview_image`) VALUES
(1, 4, 'STD2026001', 'John Doe', 'john@apex.edu', '+60 11-111 2222', 'male', '2005-03-15', 1, 'active', 0, 0, NULL),
(2, 5, 'STD2026002', 'Jane Smith', 'jane@apex.edu', '+60 11-333 4444', 'female', '2005-07-22', 1, 'active', 0, 0, NULL),
(3, 6, 'STD2026003', 'Alex Wong', 'alex@apex.edu', '+60 11-555 6666', 'other', '2004-11-09', 2, 'active', 0, 0, NULL);

-- Initial Audit Log
INSERT INTO `audit_logs` (`user_id`, `action`, `ip_address`, `user_agent`, `metadata`) VALUES
(1, 'System Installed', '127.0.0.1', 'CLI Installer', JSON_OBJECT('version', '1.0.0', 'environment', 'production'));
