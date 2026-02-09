<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_SYSTEM')) {
    exit('Stop!!!');
}

define('NV_IS_WORKMAN_ADMIN', true);

// Load CSS for workman module (frontend)
global $my_head, $global_config, $module_info, $op_file;
$css_file = NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/css/workman.css';
if (file_exists($css_file)) {
    $my_head .= '<link rel="stylesheet" href="' . NV_STATIC_URL . 'themes/' . $global_config['module_theme'] . '/css/workman.css?v=' . time() . '">' . "\n";
}

// Override layout để sử dụng layout phù hợp cho từng function
// Chỉ áp dụng cho frontend
if (!defined('NV_ADMIN')) {
    global $op;
    
    // Layout mặc định là 'main' (full-width)
    foreach ($module_info['funcs'] as $func_name => $func_values) {
        $module_info['layout_funcs'][$func_name] = 'main';
    }
    
    // Trang list sử dụng layout 'left-main' để có sidebar filter
    $module_info['layout_funcs']['list'] = 'left-main';
    
    // Đảm bảo op hiện tại cũng dùng layout phù hợp
    if (!empty($op)) {
        if ($op === 'list') {
            $module_info['layout_funcs'][$op] = 'left-main';
        } else {
            $module_info['layout_funcs'][$op] = 'main';
        }
    }
}





/**
 * Lấy danh sách categories đang active
 * 
 * @return array
 */
function workman_get_categories()
{
    global $db, $db_config, $module_data;
    
    $categories = [];
    $sql = 'SELECT id, title, color, weight FROM ' . $db_config['prefix'] . '_' . $module_data . '_categories 
            WHERE status = 1 ORDER BY weight ASC, title ASC';
    
    try {
        $result = $db->query($sql);
        while ($row = $result->fetch()) {
            $categories[$row['id']] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'color' => $row['color'],
                'weight' => $row['weight']
            ];
        }
    } catch (Exception $e) {
        trigger_error('workman_get_categories: ' . $e->getMessage());
    }
    
    return $categories;
}

/**
 * Lấy danh sách users có thể assign công việc
 * 
 * @return array
 */
function workman_get_users()
{
    global $db, $db_config;
    
    $users = [];
    // Lấy users active từ bảng users của NukeViet
    $sql = 'SELECT userid, username, first_name, last_name FROM ' . $db_config['prefix'] . '_users 
            WHERE active = 1 ORDER BY username ASC LIMIT 100';
    
    try {
        $result = $db->query($sql);
        while ($row = $result->fetch()) {
            $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
            $users[$row['userid']] = [
                'userid' => $row['userid'],
                'username' => $row['username'],
                'fullname' => !empty($fullname) ? $fullname : $row['username']
            ];
        }
    } catch (Exception $e) {
        trigger_error('workman_get_users: ' . $e->getMessage());
    }
    
    return $users;
}

/**
 * Lấy thông tin user theo ID
 * 
 * @param int $user_id
 * @return array|false
 */
function workman_get_user_info($user_id)
{
    global $db, $db_config;
    
    $user_id = intval($user_id);
    if ($user_id <= 0) {
        return false;
    }
    
    $sql = 'SELECT userid, username, first_name, last_name FROM ' . $db_config['prefix'] . '_users WHERE userid = ' . $user_id;
    
    try {
        $row = $db->query($sql)->fetch();
        if ($row) {
            $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
            return [
                'userid' => $row['userid'],
                'username' => $row['username'],
                'fullname' => !empty($fullname) ? $fullname : $row['username']
            ];
        }
    } catch (Exception $e) {
        trigger_error('workman_get_user_info: ' . $e->getMessage());
    }
    
    return false;
}

/**
 * Ghi log hoạt động
 * 
 * @param int $work_id ID công việc
 * @param string $action Hành động: created, updated, status_changed, assigned, commented, deleted
 * @param string $old_value Giá trị cũ
 * @param string $new_value Giá trị mới
 * @return bool
 */
function workman_log_activity($work_id, $action, $old_value = '', $new_value = '')
{
    global $db, $db_config, $module_data, $admin_info, $user_info;
    
    // Lấy user_id từ admin hoặc user đang đăng nhập
    $user_id = 0;
    if (!empty($admin_info['admin_id'])) {
        $user_id = $admin_info['admin_id'];
    } elseif (!empty($user_info['userid'])) {
        $user_id = $user_info['userid'];
    }
    
    $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_logs 
            (work_id, user_id, action, old_value, new_value, created_at) VALUES 
            (' . intval($work_id) . ', ' . intval($user_id) . ', ' . $db->quote($action) . ', 
             ' . $db->quote($old_value) . ', ' . $db->quote($new_value) . ', ' . NV_CURRENTTIME . ')';
    
    try {
        $db->exec($sql);
        return true;
    } catch (Exception $e) {
        trigger_error('workman_log_activity: ' . $e->getMessage());
        return false;
    }
}

/**
 * Tạo thông báo cho user
 * 
 * @param int $user_id ID người nhận
 * @param int $work_id ID công việc
 * @param string $type Loại: assigned, status_changed, commented, deadline_reminder
 * @param string $message Nội dung thông báo
 * @return bool
 */
function workman_notify($user_id, $work_id, $type, $message)
{
    global $db, $db_config, $module_data;
    
    if ($user_id <= 0) {
        return false;
    }
    
    $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_notifications 
            (user_id, work_id, type, message, is_read, created_at) VALUES 
            (' . intval($user_id) . ', ' . intval($work_id) . ', ' . $db->quote($type) . ', 
             ' . $db->quote($message) . ', 0, ' . NV_CURRENTTIME . ')';
    
    try {
        $db->exec($sql);
        return true;
    } catch (Exception $e) {
        trigger_error('workman_notify: ' . $e->getMessage());
        return false;
    }
}

/**
 * Lấy danh sách status với label
 * 
 * @return array
 */
function workman_get_status_list()
{
    global $nv_Lang;
    
    return [
        'draft' => $nv_Lang->getModule('status_draft'),
        'pending' => $nv_Lang->getModule('status_pending'),
        'doing' => $nv_Lang->getModule('status_doing'),
        'review' => $nv_Lang->getModule('status_review'),
        'done' => $nv_Lang->getModule('status_done'),
        'cancelled' => $nv_Lang->getModule('status_cancelled')
    ];
}

/**
 * Lấy danh sách priority với label
 * 
 * @return array
 */
function workman_get_priority_list()
{
    global $nv_Lang;
    
    return [
        'low' => $nv_Lang->getModule('priority_low'),
        'normal' => $nv_Lang->getModule('priority_normal'),
        'high' => $nv_Lang->getModule('priority_high'),
        'urgent' => $nv_Lang->getModule('priority_urgent')
    ];
}

/**
 * Validate chuyển đổi trạng thái theo workflow
 * 
 * @param string $old_status Trạng thái cũ
 * @param string $new_status Trạng thái mới
 * @param bool $is_admin Có phải admin không
 * @return bool
 */
function workman_validate_status_transition($old_status, $new_status, $is_admin = false)
{
    // Admin workflow
    $admin_transitions = [
        'draft' => ['pending', 'cancelled'],
        'pending' => ['doing', 'cancelled'],
        'doing' => ['review', 'cancelled'],
        'review' => ['done', 'doing'],
        'done' => [],
        'cancelled' => ['draft']
    ];
    
    // User workflow (chỉ được chuyển một số trạng thái)
    $user_transitions = [
        'pending' => ['doing'],
        'doing' => ['review'],
        'review' => [],
        'done' => [],
        'cancelled' => []
    ];
    
    $allowed = $is_admin ? $admin_transitions : $user_transitions;
    
    if (!isset($allowed[$old_status])) {
        return false;
    }
    
    return in_array($new_status, $allowed[$old_status]);
}

/**
 * Đếm số notifications chưa đọc của user
 * 
 * @param int $user_id
 * @return int
 */
function workman_count_unread_notifications($user_id)
{
    global $db, $db_config, $module_data;
    
    $sql = 'SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_' . $module_data . '_notifications 
            WHERE user_id = ' . intval($user_id) . ' AND is_read = 0';
    
    try {
        return intval($db->query($sql)->fetchColumn());
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Đánh dấu notification đã đọc
 * 
 * @param int $notification_id
 * @param int $user_id
 * @return bool
 */
function workman_mark_notification_read($notification_id, $user_id)
{
    global $db, $db_config, $module_data;
    
    $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_notifications 
            SET is_read = 1 
            WHERE id = ' . intval($notification_id) . ' AND user_id = ' . intval($user_id);
    
    try {
        $db->exec($sql);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Lấy thông tin category theo ID
 * 
 * @param int $category_id
 * @return array|false
 */
function workman_get_category_info($category_id)
{
    global $db, $db_config, $module_data;
    
    $category_id = intval($category_id);
    if ($category_id <= 0) {
        return false;
    }
    
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_categories WHERE id = ' . $category_id;
    
    try {
        $row = $db->query($sql)->fetch();
        return $row ?: false;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Đếm số task theo status của user
 * 
 * @param int $user_id
 * @return array
 */
function workman_count_tasks_by_status($user_id = 0)
{
    global $db, $db_config, $module_data;
    
    $counts = [
        'draft' => 0,
        'pending' => 0,
        'doing' => 0,
        'review' => 0,
        'done' => 0,
        'cancelled' => 0,
        'total' => 0
    ];
    
    $where = 'is_deleted = 0';
    if ($user_id > 0) {
        $where .= ' AND assigned_to = ' . intval($user_id);
    }
    
    $sql = 'SELECT status, COUNT(*) as cnt FROM ' . $db_config['prefix'] . '_' . $module_data . ' 
            WHERE ' . $where . ' GROUP BY status';
    
    try {
        $result = $db->query($sql);
        while ($row = $result->fetch()) {
            if (isset($counts[$row['status']])) {
                $counts[$row['status']] = intval($row['cnt']);
                $counts['total'] += intval($row['cnt']);
            }
        }
    } catch (Exception $e) {
        trigger_error('workman_count_tasks_by_status: ' . $e->getMessage());
    }
    
    return $counts;
}

/**
 * Trả về JSON response đúng chuẩn với HTTP header và status code
 * 
 * @param array $data Dữ liệu response
 * @param int $http_code HTTP status code (mặc định auto detect từ error field)
 * @return void
 */
function workman_json_response($data, $http_code = null)
{
    // Xóa output buffer nếu có (tránh lỗi do nội dung trước đó)
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Set Content-Type header
    header('Content-Type: application/json; charset=utf-8');
    
    // Tự động xác định HTTP status code nếu không được chỉ định
    if ($http_code === null) {
        $http_code = (!empty($data['error'])) ? 400 : 200;
    }
    
    // Set HTTP status code
    http_response_code($http_code);
    
    // Gửi response và exit
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Chuyển timestamp thành "time ago" format
 * 
 * @param int $timestamp Unix timestamp
 * @return string
 */
function workman_time_ago($timestamp)
{
    $diff = NV_CURRENTTIME - $timestamp;
    
    if ($diff < 60) {
        return 'Vừa xong';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' phút trước';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' giờ trước';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' ngày trước';
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . ' tuần trước';
    } else {
        return nv_date('d/m/Y', $timestamp);
    }
}

/**
 * Chuyển due date timestamp thành format tương đối
 * Hiển thị "Còn X ngày", "Hôm nay", "Quá hạn X ngày"
 * 
 * @param int $timestamp Unix timestamp của due date
 * @return string
 */
function workman_due_date_relative($timestamp)
{
    if ($timestamp <= 0) {
        return '';
    }
    
    $diff = $timestamp - NV_CURRENTTIME;
    $is_today = (date('Y-m-d', $timestamp) == date('Y-m-d', NV_CURRENTTIME));
    
    if ($is_today) {
        return 'Hôm nay';
    }
    
    if ($diff > 0) {
        // Tương lai
        if ($diff < 3600) {
            $mins = ceil($diff / 60);
            return 'Còn ' . $mins . ' phút';
        } elseif ($diff < 86400) {
            $hours = ceil($diff / 3600);
            return 'Còn ' . $hours . ' giờ';
        } elseif ($diff < 604800) {
            $days = ceil($diff / 86400);
            return 'Còn ' . $days . ' ngày';
        } elseif ($diff < 2592000) {
            $weeks = ceil($diff / 604800);
            return 'Còn ' . $weeks . ' tuần';
        } else {
            return nv_date('d/m/Y', $timestamp);
        }
    } else {
        // Quá hạn
        $diff = abs($diff);
        if ($diff < 3600) {
            $mins = floor($diff / 60);
            return 'Quá hạn ' . $mins . ' phút';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return 'Quá hạn ' . $hours . ' giờ';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return 'Quá hạn ' . $days . ' ngày';
        } else {
            $weeks = floor($diff / 604800);
            return 'Quá hạn ' . $weeks . ' tuần';
        }
    }
}

// ============================================================================
// SUBMISSION FUNCTIONS - Tính năng nộp kết quả công việc
// ============================================================================

/**
 * Ghi log hoạt động với dữ liệu bổ sung (extra_data)
 * 
 * @param int $work_id ID công việc
 * @param string $action Hành động
 * @param string $old_value Giá trị cũ
 * @param string $new_value Giá trị mới
 * @param array|null $extra_data Dữ liệu bổ sung (sẽ được JSON encode)
 * @return bool
 */
function workman_log_activity_ext($work_id, $action, $old_value = '', $new_value = '', $extra_data = null)
{
    global $db, $db_config, $module_data, $admin_info, $user_info;
    
    // Lấy user_id từ admin hoặc user đang đăng nhập
    $user_id = 0;
    if (!empty($admin_info['admin_id'])) {
        $user_id = $admin_info['admin_id'];
    } elseif (!empty($user_info['userid'])) {
        $user_id = $user_info['userid'];
    }
    
    $extra_json = $extra_data !== null ? json_encode($extra_data, JSON_UNESCAPED_UNICODE) : null;
    
    $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_logs 
            (work_id, user_id, action, old_value, new_value, extra_data, created_at) VALUES 
            (' . intval($work_id) . ', ' . intval($user_id) . ', ' . $db->quote($action) . ', 
             ' . $db->quote($old_value) . ', ' . $db->quote($new_value) . ', 
             ' . ($extra_json !== null ? $db->quote($extra_json) : 'NULL') . ', ' . NV_CURRENTTIME . ')';
    
    try {
        $db->exec($sql);
        return true;
    } catch (Exception $e) {
        trigger_error('workman_log_activity_ext: ' . $e->getMessage());
        return false;
    }
}

/**
 * Tạo submission mới (nộp kết quả công việc)
 * 
 * @param int $work_id ID công việc
 * @param int $user_id ID người nộp
 * @param string $description Mô tả kết quả
 * @return int|false ID của submission hoặc false nếu lỗi
 */
function workman_create_submission($work_id, $user_id, $description)
{
    global $db, $db_config, $module_data;
    
    $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_submissions 
            (work_id, user_id, description, created_at) VALUES 
            (' . intval($work_id) . ', ' . intval($user_id) . ', ' . $db->quote($description) . ', ' . NV_CURRENTTIME . ')';
    
    try {
        $db->exec($sql);
        $submission_id = $db->lastInsertId();
        
        // Log activity
        $log_preview = mb_substr(strip_tags($description), 0, 100, 'UTF-8');
        workman_log_activity_ext($work_id, 'submitted', '', $log_preview, [
            'submission_id' => $submission_id
        ]);
        
        return $submission_id;
    } catch (Exception $e) {
        trigger_error('workman_create_submission: ' . $e->getMessage());
        return false;
    }
}

/**
 * Upload file kết quả
 * 
 * @param int $submission_id ID lần nộp
 * @param int $work_id ID công việc
 * @param int $user_id ID người upload
 * @param array $file_info Thông tin file từ $_FILES
 * @return array ['success' => bool, 'file_id' => int, 'error' => string]
 */
function workman_upload_submission_file($submission_id, $work_id, $user_id, $file_info)
{
    global $db, $db_config, $module_data, $module_name;
    
    // Allowed file types
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', 'txt'];
    $max_size = 10 * 1024 * 1024; // 10MB
    
    // Validate file
    if (empty($file_info['name']) || $file_info['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload failed: ' . ($file_info['error'] ?? 'No file')];
    }
    
    if ($file_info['size'] > $max_size) {
        return ['success' => false, 'error' => 'File quá lớn (tối đa 10MB)'];
    }
    
    $ext = strtolower(pathinfo($file_info['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_types)) {
        return ['success' => false, 'error' => 'Loại file không được phép'];
    }
    
    // Create upload directory
    $upload_dir = NV_UPLOADS_DIR . '/' . $module_name . '/submissions/' . $work_id;
    $full_upload_dir = NV_ROOTDIR . '/' . $upload_dir;
    
    if (!is_dir($full_upload_dir)) {
        if (!mkdir($full_upload_dir, 0777, true) && !is_dir($full_upload_dir)) {
            return ['success' => false, 'error' => 'Không thể tạo thư mục upload'];
        }
        // Ensure index.html exists to prevent directory listing
        file_put_contents($full_upload_dir . '/index.html', '');
    }
    
    // Generate unique filename
    $new_filename = $submission_id . '_' . time() . '_' . nv_string_to_filename($file_info['name']);
    $filepath = $upload_dir . '/' . $new_filename;
    $full_filepath = NV_ROOTDIR . '/' . $filepath;
    
    // Move uploaded file
    if (!move_uploaded_file($file_info['tmp_name'], $full_filepath)) {
        $error_msg = 'Không thể lưu file (move_uploaded_file failed)';
        if (!is_writable($full_upload_dir)) {
            $error_msg .= ' - Directory not writable';
        }
        return ['success' => false, 'error' => $error_msg];
    }
    
    // Insert to database
    $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_submission_files 
            (submission_id, work_id, user_id, filename, filepath, filesize, filetype, created_at, is_deleted) VALUES 
            (' . intval($submission_id) . ', ' . intval($work_id) . ', ' . intval($user_id) . ',
             ' . $db->quote($file_info['name']) . ', ' . $db->quote($filepath) . ',
             ' . intval($file_info['size']) . ', ' . $db->quote($file_info['type']) . ',
             ' . NV_CURRENTTIME . ', 0)';
    
    try {
        $db->exec($sql);
        $file_id = $db->lastInsertId();
        
        // Log activity
        workman_log_activity_ext($work_id, 'file_uploaded', '', $file_info['name'], [
            'file_id' => $file_id,
            'submission_id' => $submission_id,
            'filesize' => $file_info['size']
        ]);
        
        return ['success' => true, 'file_id' => $file_id, 'filepath' => $filepath];
    } catch (Exception $e) {
        // Remove file if db insert failed
        @unlink(NV_ROOTDIR . '/' . $filepath);
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Xóa file kết quả (soft delete)
 * 
 * @param int $file_id ID file
 * @param int $user_id ID người xóa (kiểm tra quyền)
 * @return array ['success' => bool, 'error' => string]
 */
function workman_delete_submission_file($file_id, $user_id)
{
    global $db, $db_config, $module_data;
    
    // Get file info
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_submission_files 
            WHERE id = ' . intval($file_id) . ' AND is_deleted = 0';
    $file = $db->query($sql)->fetch();
    
    if (!$file) {
        return ['success' => false, 'error' => 'File không tồn tại'];
    }
    
    // Check permission - only owner can delete
    if ($file['user_id'] != $user_id) {
        return ['success' => false, 'error' => 'Bạn không có quyền xóa file này'];
    }
    
    // Soft delete
    $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_submission_files 
            SET is_deleted = 1, deleted_at = ' . NV_CURRENTTIME . ' 
            WHERE id = ' . intval($file_id);
    
    try {
        $db->exec($sql);
        
        // Log activity
        workman_log_activity_ext($file['work_id'], 'file_deleted', $file['filename'], '', [
            'file_id' => $file_id
        ]);
        
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error'];
    }
}

/**
 * Lấy danh sách submissions của một công việc
 * 
 * @param int $work_id ID công việc
 * @return array
 */
function workman_get_submissions($work_id)
{
    global $db, $db_config, $module_data;
    
    $submissions = [];
    
    $sql = 'SELECT s.*, u.username, u.first_name, u.last_name
            FROM ' . $db_config['prefix'] . '_' . $module_data . '_submissions s
            LEFT JOIN ' . $db_config['prefix'] . '_users u ON s.user_id = u.userid
            WHERE s.work_id = ' . intval($work_id) . '
            ORDER BY s.created_at DESC';
    
    try {
        $result = $db->query($sql);
        while ($row = $result->fetch()) {
            $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
            $row['user_fullname'] = !empty($fullname) ? $fullname : $row['username'];
            $row['created_at_formatted'] = nv_date('d/m/Y H:i', $row['created_at']);
            $row['files'] = workman_get_submission_files($row['id']);
            $submissions[] = $row;
        }
    } catch (Exception $e) {
        trigger_error('workman_get_submissions: ' . $e->getMessage());
    }
    
    return $submissions;
}

/**
 * Lấy danh sách files của một submission
 * 
 * @param int $submission_id ID lần nộp
 * @return array
 */
function workman_get_submission_files($submission_id)
{
    global $db, $db_config, $module_data;
    
    $files = [];
    
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_submission_files 
            WHERE submission_id = ' . intval($submission_id) . ' AND is_deleted = 0
            ORDER BY created_at ASC';
    
    try {
        $result = $db->query($sql);
        while ($row = $result->fetch()) {
            $row['url'] = NV_BASE_SITEURL . $row['filepath'];
            $row['created_at_formatted'] = nv_date('d/m/Y H:i', $row['created_at']);
            $row['filesize_formatted'] = workman_format_filesize($row['filesize']);
            $row['is_image'] = in_array(strtolower(pathinfo($row['filename'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            $files[] = $row;
        }
    } catch (Exception $e) {
        trigger_error('workman_get_submission_files: ' . $e->getMessage());
    }
    
    return $files;
}

/**
 * Lấy tất cả files của một công việc (không phân theo submission)
 * 
 * @param int $work_id ID công việc
 * @return array
 */
function workman_get_all_submission_files($work_id)
{
    global $db, $db_config, $module_data;
    
    $files = [];
    
    $sql = 'SELECT f.*, u.username, u.first_name, u.last_name
            FROM ' . $db_config['prefix'] . '_' . $module_data . '_submission_files f
            LEFT JOIN ' . $db_config['prefix'] . '_users u ON f.user_id = u.userid
            WHERE f.work_id = ' . intval($work_id) . ' AND f.is_deleted = 0
            ORDER BY f.created_at DESC';
    
    try {
        $result = $db->query($sql);
        while ($row = $result->fetch()) {
            $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
            $row['user_fullname'] = !empty($fullname) ? $fullname : $row['username'];
            $row['url'] = NV_BASE_SITEURL . $row['filepath'];
            $row['created_at_formatted'] = nv_date('d/m/Y H:i', $row['created_at']);
            $row['filesize_formatted'] = workman_format_filesize($row['filesize']);
            $row['is_image'] = in_array(strtolower(pathinfo($row['filename'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            $files[] = $row;
        }
    } catch (Exception $e) {
        trigger_error('workman_get_all_submission_files: ' . $e->getMessage());
    }
    
    return $files;
}

/**
 * Đếm số file đã nộp của một công việc
 * 
 * @param int $work_id ID công việc
 * @return int
 */
function workman_count_submission_files($work_id)
{
    global $db, $db_config, $module_data;
    
    $sql = 'SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_' . $module_data . '_submission_files 
            WHERE work_id = ' . intval($work_id) . ' AND is_deleted = 0';
    
    try {
        return intval($db->query($sql)->fetchColumn());
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Format filesize to human readable
 * 
 * @param int $bytes
 * @return string
 */
function workman_format_filesize($bytes)
{
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

/**
 * Kiểm tra user có thể nộp kết quả không
 * 
 * @param array $task Thông tin task
 * @param int $user_id ID user
 * @return array ['can_submit' => bool, 'reason' => string]
 */
function workman_can_submit($task, $user_id)
{
    // Phải là người được giao
    if ($task['assigned_to'] != $user_id) {
        return ['can_submit' => false, 'reason' => 'Bạn không phải người được giao công việc này'];
    }
    
    // Status phải là 'doing'
    if ($task['status'] != 'doing') {
        return ['can_submit' => false, 'reason' => 'Chỉ có thể nộp kết quả khi đang thực hiện công việc'];
    }
    
    return ['can_submit' => true, 'reason' => ''];
}

/**
 * Kiểm tra có thể gửi duyệt không (phải có ít nhất 1 file đã nộp)
 * 
 * @param int $work_id ID công việc
 * @return array ['can_review' => bool, 'reason' => string, 'file_count' => int]
 */
function workman_can_send_review($work_id)
{
    $file_count = workman_count_submission_files($work_id);
    
    if ($file_count == 0) {
        return [
            'can_review' => false, 
            'reason' => 'Phải nộp ít nhất 1 file kết quả trước khi gửi duyệt',
            'file_count' => 0
        ];
    }
    
    return ['can_review' => true, 'reason' => '', 'file_count' => $file_count];
}

/**
 * Xóa một lần nộp kết quả (Submission)
 * 
 * @param int $submission_id ID lần nộp
 * @param int $user_id ID người xóa
 * @return array ['success' => bool, 'error' => string]
 */
function workman_delete_submission($submission_id, $user_id)
{
    global $db, $db_config, $module_data;
    
    // Check if submission exists
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_submissions 
            WHERE id = ' . intval($submission_id);
    $submission = $db->query($sql)->fetch();
    
    if (!$submission) {
        return ['success' => false, 'error' => 'Lần nộp không tồn tại'];
    }
    
    try {
        // Soft delete all files related to this submission
        $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_submission_files 
                SET is_deleted = 1, deleted_at = ' . NV_CURRENTTIME . '
                WHERE submission_id = ' . intval($submission_id);
        $db->exec($sql);
        
        // Delete submission record (hard delete as it represents a grouping)
        $sql = 'DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_submissions 
                WHERE id = ' . intval($submission_id);
        $db->exec($sql);
        
        // Log activity
        $log_preview = mb_substr(strip_tags($submission['description']), 0, 100, 'UTF-8');
        workman_log_activity_ext($submission['work_id'], 'submission_deleted', '', $log_preview, [
            'submission_id' => $submission_id,
            'deleted_by' => $user_id
        ]);
        
        return ['success' => true];
    } catch (Exception $e) {
        trigger_error('workman_delete_submission: ' . $e->getMessage());
        return ['success' => false, 'error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()];
    }
}

