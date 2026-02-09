<?php

/**
 * NukeViet Content Management System - Workman Module
 * Submission API - Nộp kết quả công việc
 * @version 5.x
 * 
 * API Endpoints:
 * - POST: Tạo submission mới + upload files
 * - DELETE: Xóa file kết quả
 */

if (!defined('NV_IS_MOD_WORKMAN')) {
    exit('Stop!!!');
}

// Load helper functions FIRST
require_once NV_ROOTDIR . '/modules/' . $module_file . '/functions.php';

// Kiểm tra user đã đăng nhập
if (!defined('NV_IS_USER')) {
    workman_json_response(['error' => 1, 'message' => 'Bạn cần đăng nhập để thực hiện chức năng này'], 401);
}

$user_id = $user_info['userid'];
$method = $_SERVER['REQUEST_METHOD'];
$action = $nv_Request->get_string('action', 'request', '');

// ============================================================================
// POST: Tạo submission mới + upload files
// ============================================================================
if ($method == 'POST' && ($action == '' || $action == 'submit')) {
    
    $work_id = $nv_Request->get_int('work_id', 'post', 0);
    $description = $nv_Request->get_editor('description', '', NV_ALLOWED_HTML_TAGS);
    
    if ($work_id <= 0) {
        workman_json_response(['error' => 1, 'message' => 'Thiếu thông tin công việc']);
    }
    
    // Validate description - check plain text length
    $plain_text = trim(strip_tags($description));
    if (strlen($plain_text) < 10) {
        workman_json_response(['error' => 1, 'message' => 'Mô tả kết quả phải có ít nhất 10 ký tự']);
    }
    
    if (strlen($description) > 5000) {
        workman_json_response(['error' => 1, 'message' => 'Mô tả kết quả quá dài']);
    }
    
    // Lấy thông tin task
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . ' WHERE id = ' . $work_id . ' AND is_deleted = 0';
    $task = $db->query($sql)->fetch();
    
    if (!$task) {
        workman_json_response(['error' => 1, 'message' => 'Công việc không tồn tại']);
    }
    
    // Kiểm tra quyền nộp kết quả
    $can_submit = workman_can_submit($task, $user_id);
    if (!$can_submit['can_submit']) {
        workman_json_response(['error' => 1, 'message' => $can_submit['reason']]);
    }
    
    // Kiểm tra có file upload không
    $has_files = !empty($_FILES['files']) && is_array($_FILES['files']['name']) && !empty($_FILES['files']['name'][0]);
    
    if (!$has_files) {
        workman_json_response(['error' => 1, 'message' => 'Vui lòng đính kèm ít nhất 1 file kết quả']);
    }
    
    // Kiểm tra số lượng file
    $file_count = count(array_filter($_FILES['files']['name']));
    if ($file_count > 5) {
        workman_json_response(['error' => 1, 'message' => 'Chỉ được upload tối đa 5 file mỗi lần nộp']);
    }
    
    // Tạo submission
    $submission_id = workman_create_submission($work_id, $user_id, $description);
    
    if (!$submission_id) {
        workman_json_response(['error' => 1, 'message' => 'Không thể tạo bản nộp kết quả']);
    }
    
    // Upload files
    $uploaded_files = [];
    $upload_errors = [];
    
    for ($i = 0; $i < $file_count; $i++) {
        if ($_FILES['files']['error'][$i] === UPLOAD_ERR_OK) {
            $file_info = [
                'name' => $_FILES['files']['name'][$i],
                'type' => $_FILES['files']['type'][$i],
                'tmp_name' => $_FILES['files']['tmp_name'][$i],
                'error' => $_FILES['files']['error'][$i],
                'size' => $_FILES['files']['size'][$i]
            ];
            
            $result = workman_upload_submission_file($submission_id, $work_id, $user_id, $file_info);
            
            if ($result['success']) {
                $uploaded_files[] = [
                    'file_id' => $result['file_id'],
                    'filename' => $file_info['name'],
                    'filepath' => $result['filepath']
                ];
            } else {
                $upload_errors[] = $file_info['name'] . ': ' . $result['error'];
            }
        }
    }
    
    // Cập nhật updated_at của task
    $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . ' SET 
            updated_at = ' . NV_CURRENTTIME . ', updated_by = ' . $user_id . '
            WHERE id = ' . $work_id;
    $db->exec($sql);
    
    // Notify người giao việc
    if ($task['created_by'] > 0 && $task['created_by'] != $user_id) {
        $notify_msg = sprintf('Đã có kết quả mới cho công việc "%s"', nv_clean60($task['title'], 50));
        workman_notify($task['created_by'], $work_id, 'submitted', $notify_msg);
    }
    
    $nv_Cache->delMod($module_name);
    
    workman_json_response([
        'error' => 0,
        'message' => 'Nộp kết quả thành công!',
        'submission_id' => $submission_id,
        'uploaded_files' => $uploaded_files,
        'upload_errors' => $upload_errors,
        'total_files' => workman_count_submission_files($work_id)
    ]);
}

// ============================================================================
// POST: Xóa file kết quả
// ============================================================================
if ($method == 'POST' && $action == 'delete_file') {
    
    $file_id = $nv_Request->get_int('file_id', 'post', 0);
    
    if ($file_id <= 0) {
        workman_json_response(['error' => 1, 'message' => 'Thiếu thông tin file']);
    }
    
    // Lấy thông tin file để kiểm tra task status
    $sql = 'SELECT f.*, t.status as task_status 
            FROM ' . $db_config['prefix'] . '_' . $module_data . '_submission_files f
            LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . ' t ON f.work_id = t.id
            WHERE f.id = ' . $file_id . ' AND f.is_deleted = 0';
    $file = $db->query($sql)->fetch();
    
    if (!$file) {
        workman_json_response(['error' => 1, 'message' => 'File không tồn tại']);
    }
    
    // Chỉ cho xóa khi task đang ở trạng thái 'doing'
    if ($file['task_status'] != 'doing') {
        workman_json_response(['error' => 1, 'message' => 'Không thể xóa file khi công việc không ở trạng thái đang thực hiện']);
    }
    
    $result = workman_delete_submission_file($file_id, $user_id);
    
    if (!$result['success']) {
        workman_json_response(['error' => 1, 'message' => $result['error']]);
    }
    
    $nv_Cache->delMod($module_name);
    
    workman_json_response([
        'error' => 0,
        'message' => 'Đã xóa file thành công',
        'total_files' => workman_count_submission_files($file['work_id'])
    ]);
}

// ============================================================================
// GET: Lấy danh sách submissions của task
// ============================================================================
if ($method == 'GET' && $action == 'list') {
    
    $work_id = $nv_Request->get_int('work_id', 'get', 0);
    
    if ($work_id <= 0) {
        workman_json_response(['error' => 1, 'message' => 'Thiếu thông tin công việc']);
    }
    
    // Kiểm tra task tồn tại và user có quyền xem
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . ' WHERE id = ' . $work_id . ' AND is_deleted = 0';
    $task = $db->query($sql)->fetch();
    
    if (!$task) {
        workman_json_response(['error' => 1, 'message' => 'Công việc không tồn tại']);
    }
    
    // Cho phép cả assignee và creator xem
    if ($task['assigned_to'] != $user_id && $task['created_by'] != $user_id) {
        workman_json_response(['error' => 1, 'message' => 'Bạn không có quyền xem kết quả công việc này']);
    }
    
    $submissions = workman_get_submissions($work_id);
    
    workman_json_response([
        'error' => 0,
        'submissions' => $submissions,
        'total_files' => workman_count_submission_files($work_id)
    ]);
}

// ============================================================================
// POST: Xóa lần nộp kết quả
// ============================================================================
if ($method == 'POST' && $action == 'delete_submission') {
    
    $submission_id = $nv_Request->get_int('submission_id', 'post', 0);
    
    if ($submission_id <= 0) {
        workman_json_response(['error' => 1, 'message' => 'Thiếu thông tin lần nộp']);
    }
    
    // Lấy thông tin submission để kiểm tra task status
    $sql = 'SELECT s.*, t.status as task_status, t.assigned_to 
            FROM ' . $db_config['prefix'] . '_' . $module_data . '_submissions s
            LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . ' t ON s.work_id = t.id
            WHERE s.id = ' . $submission_id;
    $submission = $db->query($sql)->fetch();
    
    if (!$submission) {
        workman_json_response(['error' => 1, 'message' => 'Lần nộp không tồn tại']);
    }
    
    // Chỉ cho xóa khi task đang ở trạng thái 'doing'
    if ($submission['task_status'] != 'doing') {
        workman_json_response(['error' => 1, 'message' => 'Chỉ có thể xóa khi công việc đang thực hiện']);
    }
    
    // Check permission: chỉ người tạo submission hoặc admin mới được xóa
    if ($submission['user_id'] != $user_id && !defined('NV_IS_ADMIN')) {
        workman_json_response(['error' => 1, 'message' => 'Bạn không có quyền xóa lần nộp này']);
    }
    
    $result = workman_delete_submission($submission_id, $user_id);
    
    if (!$result['success']) {
        workman_json_response(['error' => 1, 'message' => $result['error']]);
    }
    
    $nv_Cache->delMod($module_name);
    
    workman_json_response([
        'error' => 0,
        'message' => 'Đã xóa lần nộp thành công',
        'total_files' => workman_count_submission_files($submission['work_id'])
    ]);
}

// ============================================================================
// Invalid request
// ============================================================================
workman_json_response(['error' => 1, 'message' => 'Invalid request']);
