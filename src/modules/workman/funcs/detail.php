<?php

/**
 * NukeViet Content Management System - Workman Module
 * Task Detail (Frontend)
 * @version 5.x
 */

if (!defined('NV_IS_MOD_WORKMAN')) {
    exit('Stop!!!');
}

// Kiểm tra user đã đăng nhập
if (!defined('NV_IS_USER')) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt(NV_MY_DOMAIN . NV_REQUEST_URI));
}

// Load helper functions
require_once NV_ROOTDIR . '/modules/' . $module_file . '/functions.php';

$user_id = $user_info['userid'];
$id = $nv_Request->get_int('id', 'get', 0);

if ($id <= 0) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

// Lấy thông tin task
$sql = 'SELECT w.*, c.title as category_title, c.color as category_color,
        u.username as creator_username, u.first_name as creator_first_name, u.last_name as creator_last_name
        FROM ' . $db_config['prefix'] . '_' . $module_data . ' w
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . '_categories c ON w.category_id = c.id
        LEFT JOIN ' . $db_config['prefix'] . '_users u ON w.created_by = u.userid
        WHERE w.id = ' . $id . ' AND w.is_deleted = 0';

$task = $db->query($sql)->fetch();

if (!$task) {
    // Không tìm thấy task
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

// Kiểm tra quyền xem: phải là người được assign hoặc người tạo task
$is_assigned = ($task['assigned_to'] == $user_id);
$is_creator = ($task['created_by'] == $user_id);

if (!$is_assigned && !$is_creator) {
    // Không có quyền xem task này
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

$page_title = $task['title'];

// Format data
$task['due_date_formatted'] = $task['due_date'] > 0 ? nv_date('d/m/Y H:i', $task['due_date']) : '';
$task['created_at_formatted'] = $task['created_at'] > 0 ? nv_date('d/m/Y H:i', $task['created_at']) : '';
$task['updated_at_formatted'] = $task['updated_at'] > 0 ? nv_date('d/m/Y H:i', $task['updated_at']) : '';
$task['is_overdue'] = ($task['due_date'] > 0 && $task['due_date'] < NV_CURRENTTIME && !in_array($task['status'], ['done', 'cancelled']));
$task['status_text'] = $nv_Lang->getModule('status_' . $task['status']) ?: $task['status'];
$task['status_class'] = $nv_Lang->getModule('status_class_' . $task['status']) ?: 'secondary';
$task['priority_text'] = $nv_Lang->getModule('priority_' . $task['priority']) ?: $task['priority'];
$task['priority_class'] = $nv_Lang->getModule('priority_class_' . $task['priority']) ?: 'info';

$creator_fullname = trim($task['creator_first_name'] . ' ' . $task['creator_last_name']);
$task['creator_name'] = !empty($creator_fullname) ? $creator_fullname : $task['creator_username'];

// Attachment
if (!empty($task['attachment'])) {
    $task['attachment_name'] = basename($task['attachment']);
    $task['attachment_url'] = NV_BASE_SITEURL . $task['attachment'];
    $task['is_image'] = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $task['attachment']);
}

// ============================================================================
// Lấy comments
// ============================================================================
$comments = [];
$sql = 'SELECT cm.*, u.username, u.first_name, u.last_name
        FROM ' . $db_config['prefix'] . '_' . $module_data . '_comments cm
        LEFT JOIN ' . $db_config['prefix'] . '_users u ON cm.user_id = u.userid
        WHERE cm.work_id = ' . $id . '
        ORDER BY cm.created_at ASC';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
        $row['user_fullname'] = !empty($fullname) ? $fullname : $row['username'];
        $row['created_at_formatted'] = nv_date('d/m/Y H:i', $row['created_at']);
        if (!empty($row['attachment'])) {
            $row['attachment_name'] = basename($row['attachment']);
            $row['attachment_url'] = NV_BASE_SITEURL . $row['attachment'];
        }
        $comments[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Lấy activity logs
// ============================================================================
$logs = [];
$sql = 'SELECT l.*, u.username, u.first_name, u.last_name
        FROM ' . $db_config['prefix'] . '_' . $module_data . '_logs l
        LEFT JOIN ' . $db_config['prefix'] . '_users u ON l.user_id = u.userid
        WHERE l.work_id = ' . $id . '
        ORDER BY l.created_at DESC
        LIMIT 20';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
        $row['user_fullname'] = !empty($fullname) ? $fullname : $row['username'];
        $row['created_at_formatted'] = nv_date('d/m/Y H:i', $row['created_at']);
        $row['action_text'] = $nv_Lang->getModule('log_' . $row['action']) ?: $row['action'];
        $logs[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// Đánh dấu thông báo liên quan đã đọc
$sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_notifications 
        SET is_read = 1 WHERE user_id = ' . $user_id . ' AND work_id = ' . $id;
$db->exec($sql);

// ============================================================================
// Render template
// ============================================================================
$xtpl = new XTemplate('detail.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);

$xtpl->assign('LANG', $nv_Lang->getModule());
$xtpl->assign('GLANG', $nv_Lang->getGlobal());
$xtpl->assign('TASK', $task);

// URLs
$url_list = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=list';
$url_update = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=update';
$url_comment = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=comment';

$xtpl->assign('URL_LIST', $url_list);
$xtpl->assign('URL_UPDATE', $url_update);
$xtpl->assign('URL_COMMENT', $url_comment);
$xtpl->assign('TASK_ID', $id);

// Show action buttons based on status
if ($task['status'] == 'pending') {
    $xtpl->parse('main.action_accept'); // Nhận việc: pending -> doing
}
if ($task['status'] == 'doing') {
    $xtpl->parse('main.action_review'); // Yêu cầu duyệt: doing -> review
}

// Comments
foreach ($comments as $comment) {
    $xtpl->assign('COMMENT', $comment);
    if (!empty($comment['attachment'])) {
        $xtpl->parse('main.comment.attachment');
    }
    $xtpl->parse('main.comment');
}

if (empty($comments)) {
    $xtpl->parse('main.no_comments');
}

// Activity logs
foreach ($logs as $log) {
    $xtpl->assign('LOG', $log);
    $xtpl->parse('main.log');
}

if (empty($logs)) {
    $xtpl->parse('main.no_logs');
}


// Attachment
if (!empty($task['attachment'])) {
    if ($task['is_image']) {
        $xtpl->parse('main.attachment_image');
    } else {
        $xtpl->parse('main.attachment_file');
    }
}

// Comment form (chỉ hiện nếu task chưa done/cancelled và user có quyền comment)
if (!in_array($task['status'], ['done', 'cancelled']) && ($is_assigned || $is_creator)) {
    $xtpl->parse('main.comment_form');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
