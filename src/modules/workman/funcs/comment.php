<?php

/**
 * NukeViet Content Management System - Workman Module
 * Add Comment (Frontend - AJAX/POST)
 * @version 5.x
 */

if (!defined('NV_IS_MOD_WORKMAN')) {
    exit('Stop!!!');
}

// Kiểm tra user đã đăng nhập
if (!defined('NV_IS_USER')) {
    if ($nv_Request->isset_request('ajax', 'post')) {
        workman_json_response(['error' => 1, 'message' => 'Not logged in']);
    }
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login');
}

// Load helper functions
require_once NV_ROOTDIR . '/modules/' . $module_file . '/functions.php';

// Định nghĩa thư mục upload cho comments
if (!defined('NV_WORKMAN_UPLOAD_DIR')) {
    define('NV_WORKMAN_UPLOAD_DIR', NV_UPLOADS_DIR . '/workman');
}
if (!defined('NV_WORKMAN_UPLOAD_REAL_DIR')) {
    define('NV_WORKMAN_UPLOAD_REAL_DIR', NV_UPLOADS_REAL_DIR . '/workman');
}

// Tạo thư mục nếu chưa có
if (!is_dir(NV_WORKMAN_UPLOAD_REAL_DIR)) {
    nv_mkdir(NV_ROOTDIR . '/' . NV_WORKMAN_UPLOAD_DIR, NV_WORKMAN_UPLOAD_DIR);
}

$user_id = $user_info['userid'];

// Chỉ xử lý POST request
if ($nv_Request->get_string('REQUEST_METHOD', 'server') != 'POST') {
    workman_json_response(['error' => 1, 'message' => 'Invalid request']);
}

$work_id = $nv_Request->get_int('work_id', 'post', 0);
$content = $nv_Request->get_textarea('content', '', 'post');
$is_ajax = $nv_Request->get_int('ajax', 'post', 0);

if ($work_id <= 0) {
    if ($is_ajax) {
        workman_json_response(['error' => 1, 'message' => $nv_Lang->getModule('error_not_found')]);
    }
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

// Lấy thông tin task
$sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . ' WHERE id = ' . $work_id . ' AND is_deleted = 0';
$task = $db->query($sql)->fetch();

if (!$task) {
    if ($is_ajax) {
        workman_json_response(['error' => 1, 'message' => $nv_Lang->getModule('error_not_found')]);
    }
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

// Kiểm tra quyền - người được assign hoặc người tạo task (admin) mới được comment
$is_creator = ($task['created_by'] == $user_id);
$is_assigned = ($task['assigned_to'] == $user_id);

if (!$is_creator && !$is_assigned) {
    if ($is_ajax) {
        workman_json_response(['error' => 1, 'message' => $nv_Lang->getModule('error_permission_denied')]);
    }
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

// Kiểm tra task chưa done/cancelled
if (in_array($task['status'], ['done', 'cancelled'])) {
    if ($is_ajax) {
        workman_json_response(['error' => 1, 'message' => $nv_Lang->getModule('error_permission_denied')]);
    }
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=detail&id=' . $work_id);
}

// Validation
if (empty(trim($content))) {
    if ($is_ajax) {
        workman_json_response(['error' => 1, 'message' => 'Nội dung bình luận không được để trống']);
    }
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=detail&id=' . $work_id);
}

$attachment = '';

// Xử lý file upload
if (isset($_FILES['attachment']) && is_uploaded_file($_FILES['attachment']['tmp_name'])) {
    $upload = new NukeViet\Files\Upload(
        ['images', 'documents', 'archives', 'adobe'],
        $global_config['forbid_extensions'], 
        $global_config['forbid_mimes'], 
        NV_UPLOAD_MAX_FILESIZE, 
        NV_MAX_WIDTH, 
        NV_MAX_HEIGHT
    );
    $upload->setLanguage(\NukeViet\Core\Language::$lang_global);
    $upload_info = $upload->save_file(
        $_FILES['attachment'], 
        NV_WORKMAN_UPLOAD_REAL_DIR, 
        false,
        $global_config['nv_auto_resize']
    );
    @unlink($_FILES['attachment']['tmp_name']);
    
    if (empty($upload_info['error'])) {
        @chmod($upload_info['name'], 0644);
        $attachment = NV_WORKMAN_UPLOAD_DIR . '/' . $upload_info['basename'];
    }
}

// Insert comment
try {
    $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_comments 
            (work_id, user_id, content, attachment, created_at) VALUES (
            ' . $work_id . ',
            ' . $user_id . ',
            ' . $db->quote($content) . ',
            ' . $db->quote($attachment) . ',
            ' . NV_CURRENTTIME . '
        )';
    $db->exec($sql);
    
    // Log activity
    workman_log_activity($work_id, 'commented');
    
    // Notify các bên liên quan
    // Nếu user comment thì notify admin (người tạo)
    if ($task['created_by'] > 0 && $task['created_by'] != $user_id) {
        $notify_msg = sprintf($nv_Lang->getModule('notification_commented'), $task['title']);
        workman_notify($task['created_by'], $work_id, 'commented', $notify_msg);
    }
    // Nếu admin comment thì notify user (người được assign)
    if ($task['assigned_to'] > 0 && $task['assigned_to'] != $user_id) {
        $notify_msg = sprintf($nv_Lang->getModule('notification_admin_commented'), $task['title']);
        workman_notify($task['assigned_to'], $work_id, 'commented', $notify_msg);
    }
    
    $nv_Cache->delMod($module_name);
    
    if ($is_ajax) {
        $fullname = trim($user_info['first_name'] . ' ' . $user_info['last_name']);
        $user_fullname = !empty($fullname) ? $fullname : $user_info['username'];
        
        workman_json_response([
            'error' => 0,
            'message' => 'Thêm bình luận thành công',
            'comment' => [
                'user_fullname' => $user_fullname,
                'content' => nl2br(nv_htmlspecialchars($content)),
                'created_at_formatted' => nv_date('d/m/Y H:i', NV_CURRENTTIME),
                'attachment' => $attachment,
                'attachment_name' => !empty($attachment) ? basename($attachment) : '',
                'attachment_url' => !empty($attachment) ? NV_BASE_SITEURL . $attachment : ''
            ]
        ]);
    }
    
} catch (Exception $e) {
    if ($is_ajax) {
        workman_json_response(['error' => 1, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Redirect về trang detail
nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=detail&id=' . $work_id);
