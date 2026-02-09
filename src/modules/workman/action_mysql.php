<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_MODULES')) {
    exit('Stop!!!');
}

$sql_drop_module = [];

// Drop tables in reverse order (child tables first)
$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $db_config['prefix'] . '_' . $module_data . '_notifications;';
$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $db_config['prefix'] . '_' . $module_data . '_logs;';
$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $db_config['prefix'] . '_' . $module_data . '_submission_files;';
$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $db_config['prefix'] . '_' . $module_data . '_submissions;';
$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $db_config['prefix'] . '_' . $module_data . '_comments;';
$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $db_config['prefix'] . '_' . $module_data . '_categories;';
$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $db_config['prefix'] . '_' . $module_data . ';';

$sql_create_module = $sql_drop_module;

// ============================================================================
// Bảng chính: workman - Quản lý công việc
// ============================================================================
$sql_create_module[] = 'CREATE TABLE ' . $db_config['prefix'] . '_' . $module_data . " (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` varchar(250) NOT NULL COMMENT 'Tiêu đề công việc',
    `description` mediumtext COMMENT 'Mô tả chi tiết',
    `status` varchar(50) DEFAULT 'draft' COMMENT 'Trạng thái: draft, pending, doing, review, done, cancelled',
    `priority` varchar(50) DEFAULT 'normal' COMMENT 'Độ ưu tiên: low, normal, high, urgent',
    `due_date` int(11) DEFAULT 0 COMMENT 'Ngày hết hạn (unix timestamp)',
    `start_at` int(11) DEFAULT NULL COMMENT 'Thời gian bắt đầu làm (khi chuyển pending -> doing)',
    `attachment` varchar(255) DEFAULT '' COMMENT 'File đính kèm từ người giao',
    `created_by` int(11) UNSIGNED DEFAULT 0 COMMENT 'ID người tạo/giao việc',
    `assigned_to` int(11) UNSIGNED DEFAULT 0 COMMENT 'ID người được giao/thực hiện',
    `category_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID danh mục',
    `created_at` int(11) DEFAULT 0 COMMENT 'Thời gian tạo',
    `updated_at` int(11) DEFAULT 0 COMMENT 'Thời gian cập nhật',
    `updated_by` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID người cập nhật cuối',
    `completed_at` int(11) DEFAULT NULL COMMENT 'Thời gian hoàn thành',
    `is_deleted` tinyint(1) DEFAULT 0 COMMENT 'Đã xóa mềm',
    `deleted_at` int(11) DEFAULT NULL COMMENT 'Thời gian xóa',
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_assigned_to` (`assigned_to`),
    KEY `idx_created_by` (`created_by`),
    KEY `idx_category` (`category_id`),
    KEY `idx_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// ============================================================================
// Bảng danh mục: workman_categories
// ============================================================================
$sql_create_module[] = 'CREATE TABLE ' . $db_config['prefix'] . '_' . $module_data . "_categories (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` varchar(150) NOT NULL COMMENT 'Tên danh mục',
    `description` varchar(255) DEFAULT '' COMMENT 'Mô tả',
    `color` varchar(7) DEFAULT '#3498db' COMMENT 'Màu hiển thị',
    `weight` int(11) DEFAULT 0 COMMENT 'Thứ tự sắp xếp',
    `status` tinyint(1) DEFAULT 1 COMMENT 'Trạng thái: 1=active, 0=inactive',
    PRIMARY KEY (`id`),
    KEY `idx_weight` (`weight`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// ============================================================================
// Bảng bình luận: workman_comments - Chỉ dùng cho trao đổi/thảo luận
// ============================================================================
$sql_create_module[] = 'CREATE TABLE ' . $db_config['prefix'] . '_' . $module_data . "_comments (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `work_id` int(11) UNSIGNED NOT NULL COMMENT 'ID công việc',
    `user_id` int(11) UNSIGNED NOT NULL COMMENT 'ID người bình luận',
    `content` text NOT NULL COMMENT 'Nội dung bình luận',
    `attachment` varchar(255) DEFAULT '' COMMENT 'File đính kèm',
    `created_at` int(11) DEFAULT 0 COMMENT 'Thời gian tạo',
    PRIMARY KEY (`id`),
    KEY `idx_work_id` (`work_id`),
    KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// ============================================================================
// Bảng kết quả công việc: workman_submissions
// Lưu các lần nộp kết quả của người thực hiện
// ============================================================================
$sql_create_module[] = 'CREATE TABLE ' . $db_config['prefix'] . '_' . $module_data . "_submissions (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `work_id` int(11) UNSIGNED NOT NULL COMMENT 'ID công việc',
    `user_id` int(11) UNSIGNED NOT NULL COMMENT 'ID người nộp',
    `description` text COMMENT 'Mô tả kết quả công việc',
    `created_at` int(11) DEFAULT 0 COMMENT 'Thời gian nộp',
    PRIMARY KEY (`id`),
    KEY `idx_work_id` (`work_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// ============================================================================
// Bảng file kết quả: workman_submission_files
// Lưu các file đính kèm trong mỗi lần nộp kết quả
// ============================================================================
$sql_create_module[] = 'CREATE TABLE ' . $db_config['prefix'] . '_' . $module_data . "_submission_files (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `submission_id` int(11) UNSIGNED NOT NULL COMMENT 'ID lần nộp kết quả',
    `work_id` int(11) UNSIGNED NOT NULL COMMENT 'ID công việc (denormalized for query)',
    `user_id` int(11) UNSIGNED NOT NULL COMMENT 'ID người upload',
    `filename` varchar(255) NOT NULL COMMENT 'Tên file gốc',
    `filepath` varchar(500) NOT NULL COMMENT 'Đường dẫn file trên server',
    `filesize` int(11) UNSIGNED DEFAULT 0 COMMENT 'Kích thước file (bytes)',
    `filetype` varchar(100) DEFAULT '' COMMENT 'MIME type',
    `created_at` int(11) DEFAULT 0 COMMENT 'Thời gian upload',
    `is_deleted` tinyint(1) DEFAULT 0 COMMENT 'Đã xóa: 0=không, 1=đã xóa',
    `deleted_at` int(11) DEFAULT NULL COMMENT 'Thời gian xóa',
    PRIMARY KEY (`id`),
    KEY `idx_submission_id` (`submission_id`),
    KEY `idx_work_id` (`work_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// ============================================================================
// Bảng logs: workman_logs - Ghi lại lịch sử thay đổi
// ============================================================================
$sql_create_module[] = 'CREATE TABLE ' . $db_config['prefix'] . '_' . $module_data . "_logs (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `work_id` int(11) UNSIGNED NOT NULL COMMENT 'ID công việc',
    `user_id` int(11) UNSIGNED NOT NULL COMMENT 'ID người thực hiện',
    `action` varchar(50) NOT NULL COMMENT 'Hành động: created, updated, status_changed, assigned, commented, submitted, file_uploaded, file_deleted, deleted',
    `old_value` varchar(255) DEFAULT '' COMMENT 'Giá trị cũ',
    `new_value` varchar(255) DEFAULT '' COMMENT 'Giá trị mới',
    `extra_data` text COMMENT 'Dữ liệu bổ sung (JSON)',
    `created_at` int(11) DEFAULT 0 COMMENT 'Thời gian',
    PRIMARY KEY (`id`),
    KEY `idx_work_id` (`work_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_action` (`action`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// ============================================================================
// Bảng thông báo: workman_notifications
// ============================================================================
$sql_create_module[] = 'CREATE TABLE ' . $db_config['prefix'] . '_' . $module_data . "_notifications (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` int(11) UNSIGNED NOT NULL COMMENT 'ID người nhận',
    `work_id` int(11) UNSIGNED NOT NULL COMMENT 'ID công việc',
    `type` varchar(50) NOT NULL COMMENT 'Loại: assigned, status_changed, commented, deadline_reminder',
    `message` varchar(255) NOT NULL COMMENT 'Nội dung thông báo',
    `is_read` tinyint(1) DEFAULT 0 COMMENT 'Đã đọc: 0=chưa, 1=đã đọc',
    `created_at` int(11) DEFAULT 0 COMMENT 'Thời gian',
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_work_id` (`work_id`),
    KEY `idx_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// ============================================================================
// Insert default category
// ============================================================================
$sql_create_module[] = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . "_categories 
    (`title`, `description`, `color`, `weight`, `status`) VALUES 
    ('Mặc định', 'Danh mục mặc định', '#3498db', 0, 1)";
