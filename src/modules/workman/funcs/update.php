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
    die(json_encode(['error' => 1, 'message' => 'Not logged in']));
}

// Load helper functions
require_once NV_ROOTDIR . '/modules/' . $module_file . '/functions.php';

$user_id = $user_info['userid'];

// Chỉ xử lý POST request
if ($nv_Request->get_string('method', 'server') != 'POST') {
    die(json_encode(['error' => 1, 'message' => 'Invalid request']));
}

$id = $nv_Request->get_int('id', 'post', 0);
$new_status = $nv_Request->get_string('status', 'post', '');

if ($id <= 0 || empty($new_status)) {
    die(json_encode(['error' => 1, 'message' => $nv_Lang->getModule('error_invalid_status')]));
}

// Lấy thông tin task
$sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . ' WHERE id = ' . $id . ' AND is_deleted = 0';
$task = $db->query($sql)->fetch();

if (!$task) {
    die(json_encode(['error' => 1, 'message' => $nv_Lang->getModule('error_not_found')]));
}

// Kiểm tra quyền - chỉ người được assign mới được update
if ($task['assigned_to'] != $user_id) {
    die(json_encode(['error' => 1, 'message' => $nv_Lang->getModule('error_permission_denied')]));
}

$old_status = $task['status'];

// Validate workflow transition cho user (không phải admin)
if (!workman_validate_status_transition($old_status, $new_status, false)) {
    die(json_encode(['error' => 1, 'message' => $nv_Lang->getModule('error_invalid_transition')]));
}

// Cập nhật status
try {
    $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . ' SET 
            status = ' . $db->quote($new_status) . ',
            updated_at = ' . NV_CURRENTTIME . '
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
    
    die(json_encode([
        'error' => 0, 
        'message' => $nv_Lang->getModule('success_status_updated'),
        'new_status' => $new_status,
        'new_status_text' => $new_status_text,
        'new_status_class' => $new_status_class
    ]));
    
} catch (Exception $e) {
    die(json_encode(['error' => 1, 'message' => 'Database error: ' . $e->getMessage()]));
}
