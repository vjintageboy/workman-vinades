<?php

/**
 * NukeViet Content Management System - Workman Module
 * Update Task Status (Frontend - AJAX)
 * @version 5.x
 */

if (!defined('NV_IS_MOD_WORKMAN')) {
    exit('Stop!!!');
}

// Kiểm tra user đã đăng nhập
if (!defined('NV_IS_USER')) {
    workman_json_response(['error' => 1, 'message' => 'Not logged in']);
}

// Load helper functions
require_once NV_ROOTDIR . '/modules/' . $module_file . '/functions.php';

$user_id = $user_info['userid'];

// Chỉ xử lý POST request
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    workman_json_response(['error' => 1, 'message' => 'Invalid request method']);
}

$id = $nv_Request->get_int('id', 'post', 0);
$new_status = $nv_Request->get_string('status', 'post', '');

if ($id <= 0 || empty($new_status)) {
    workman_json_response(['error' => 1, 'message' => $nv_Lang->getModule('error_invalid_status')]);
}

// Lấy thông tin task
$sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . ' WHERE id = ' . $id . ' AND is_deleted = 0';
$task = $db->query($sql)->fetch();

if (!$task) {
    workman_json_response(['error' => 1, 'message' => $nv_Lang->getModule('error_not_found')]);
}

// Kiểm tra quyền - chỉ người được assign mới được update
if ($task['assigned_to'] != $user_id) {
    workman_json_response(['error' => 1, 'message' => $nv_Lang->getModule('error_permission_denied')]);
}

$old_status = $task['status'];

// Validate workflow transition cho user (không phải admin)
if (!workman_validate_status_transition($old_status, $new_status, false)) {
    workman_json_response(['error' => 1, 'message' => $nv_Lang->getModule('error_invalid_transition')]);
}

// Kiểm tra đặc biệt khi chuyển sang review - phải có ít nhất 1 file đã nộp
if ($new_status == 'review') {
    $review_check = workman_can_send_review($id);
    if (!$review_check['can_review']) {
        workman_json_response([
            'error' => 1, 
            'message' => $review_check['reason'],
            'need_submit' => true // Flag để frontend biết cần nộp kết quả trước
        ]);
    }
}

// Cập nhật status
try {
    // Xây dựng câu SQL update
    $update_fields = [
        'status = ' . $db->quote($new_status),
        'updated_at = ' . NV_CURRENTTIME,
        'updated_by = ' . $user_id
    ];
    
    // Nếu chuyển từ pending → doing: set start_at
    if ($old_status == 'pending' && $new_status == 'doing') {
        $update_fields[] = 'start_at = ' . NV_CURRENTTIME;
    }
    
    // Nếu chuyển sang done: set completed_at
    if ($new_status == 'done') {
        $update_fields[] = 'completed_at = ' . NV_CURRENTTIME;
    }
    
    $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . ' SET 
            ' . implode(', ', $update_fields) . '
            WHERE id = ' . $id;
    $db->exec($sql);
    
    // Log activity
    workman_log_activity($id, 'status_changed', $old_status, $new_status);
    
    // Notify admin (người tạo task) về thay đổi status
    if ($task['created_by'] > 0 && $task['created_by'] != $user_id) {
        $status_text = $nv_Lang->getModule('status_' . $new_status) ?: $new_status;
        $notify_msg = sprintf($nv_Lang->getModule('notification_status_changed'), $task['title'], $status_text);
        workman_notify($task['created_by'], $id, 'status_changed', $notify_msg);
    }
    
    $nv_Cache->delMod($module_name);
    
    $new_status_text = $nv_Lang->getModule('status_' . $new_status) ?: $new_status;
    $new_status_class = $nv_Lang->getModule('status_class_' . $new_status) ?: 'secondary';
    
    workman_json_response([
        'error' => 0, 
        'message' => $nv_Lang->getModule('success_status_updated'),
        'new_status' => $new_status,
        'new_status_text' => $new_status_text,
        'new_status_class' => $new_status_class
    ]);
    
} catch (Exception $e) {
    workman_json_response(['error' => 1, 'message' => 'Database error: ' . $e->getMessage()]);
}

