<?php

/**
 * NukeViet Content Management System - Workman Module
 * User Dashboard (Frontend)
 * @version 5.x
 */

if (!defined('NV_IS_MOD_WORKMAN')) {
    exit('Stop!!!');
}

// Kiểm tra user đã đăng nhập
if (!defined('NV_IS_USER')) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt(NV_MY_DOMAIN . NV_REQUEST_URI));
}

$page_title = $nv_Lang->getModule('dashboard');

// Load helper functions
require_once NV_ROOTDIR . '/modules/' . $module_file . '/functions.php';

// Lấy stats của user hiện tại
$user_id = $user_info['userid'];
$stats = workman_count_tasks_by_status($user_id);

// ============================================================================
// Lấy công việc mới (pending) - cần nhận
// ============================================================================
$pending_tasks = [];
$sql = 'SELECT w.*, c.title as category_title, c.color as category_color
        FROM ' . $db_config['prefix'] . '_' . $module_data . ' w
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . '_categories c ON w.category_id = c.id
        WHERE w.assigned_to = ' . $user_id . ' AND w.is_deleted = 0 AND w.status = "pending"
        ORDER BY w.due_date ASC, w.id DESC
        LIMIT 5';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $row['due_date_formatted'] = $row['due_date'] > 0 ? nv_date('d/m/Y', $row['due_date']) : '';
        $pending_tasks[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Lấy công việc đang làm
// ============================================================================
$doing_tasks = [];
$sql = 'SELECT w.*, c.title as category_title, c.color as category_color
        FROM ' . $db_config['prefix'] . '_' . $module_data . ' w
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . '_categories c ON w.category_id = c.id
        WHERE w.assigned_to = ' . $user_id . ' AND w.is_deleted = 0 AND w.status = "doing"
        ORDER BY w.due_date ASC, w.id DESC
        LIMIT 5';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $row['due_date_formatted'] = $row['due_date'] > 0 ? nv_date('d/m/Y', $row['due_date']) : '';
        $row['is_overdue'] = ($row['due_date'] > 0 && $row['due_date'] < NV_CURRENTTIME);
        $doing_tasks[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Lấy công việc chờ duyệt
// ============================================================================
$review_tasks = [];
$sql = 'SELECT w.*, c.title as category_title, c.color as category_color
        FROM ' . $db_config['prefix'] . '_' . $module_data . ' w
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . '_categories c ON w.category_id = c.id
        WHERE w.assigned_to = ' . $user_id . ' AND w.is_deleted = 0 AND w.status = "review"
        ORDER BY w.id DESC
        LIMIT 5';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $row['due_date_formatted'] = $row['due_date'] > 0 ? nv_date('d/m/Y', $row['due_date']) : '';
        $review_tasks[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Lấy thông báo chưa đọc
// ============================================================================
$notifications = [];
$sql = 'SELECT n.*, w.title as work_title
        FROM ' . $db_config['prefix'] . '_' . $module_data . '_notifications n
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . ' w ON n.work_id = w.id
        WHERE n.user_id = ' . $user_id . ' AND n.is_read = 0
        ORDER BY n.created_at DESC
        LIMIT 5';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $row['created_at_formatted'] = nv_date('d/m/Y H:i', $row['created_at']);
        $notifications[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

$unread_count = count($notifications);

// ============================================================================
// Render template
// ============================================================================
$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);

$xtpl->assign('LANG', $nv_Lang->getModule());
$xtpl->assign('GLANG', $nv_Lang->getGlobal());
$xtpl->assign('STATS', $stats);
$xtpl->assign('USER_NAME', $user_info['username']);
$xtpl->assign('UNREAD_COUNT', $unread_count);

// URLs
$url_list = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=list';
$url_detail_base = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=detail&id=';

$xtpl->assign('URL_LIST', $url_list);

// Pending tasks
foreach ($pending_tasks as $task) {
    $task['url_detail'] = $url_detail_base . $task['id'];
    $xtpl->assign('PENDING', $task);
    $xtpl->parse('main.pending_task');
}

if (empty($pending_tasks)) {
    $xtpl->parse('main.no_pending');
}

// Doing tasks
foreach ($doing_tasks as $task) {
    $task['url_detail'] = $url_detail_base . $task['id'];
    $xtpl->assign('DOING', $task);
    if ($task['is_overdue']) {
        $xtpl->parse('main.doing_task.overdue');
    }
    $xtpl->parse('main.doing_task');
}

if (empty($doing_tasks)) {
    $xtpl->parse('main.no_doing');
}

// Review tasks
foreach ($review_tasks as $task) {
    $task['url_detail'] = $url_detail_base . $task['id'];
    $xtpl->assign('REVIEW', $task);
    $xtpl->parse('main.review_task');
}

if (empty($review_tasks)) {
    $xtpl->parse('main.no_review');
}

// Notifications
foreach ($notifications as $notif) {
    $notif['url_detail'] = $url_detail_base . $notif['work_id'];
    $xtpl->assign('NOTIF', $notif);
    $xtpl->parse('main.notification');
}

if (empty($notifications)) {
    $xtpl->parse('main.no_notifications');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
