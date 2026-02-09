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

// Tính progress percentage
$total_tasks = $stats['pending'] + $stats['doing'] + $stats['review'] + $stats['done'];
$progress_percent = $total_tasks > 0 ? round(($stats['done'] / $total_tasks) * 100) : 0;

// ============================================================================
// Lấy tất cả công việc (pending, doing, review) trong 1 query duy nhất
// ============================================================================
$all_tasks = ['pending' => [], 'doing' => [], 'review' => []];
$sql = 'SELECT w.*, c.title as category_title, c.color as category_color
        FROM ' . $db_config['prefix'] . '_' . $module_data . ' w
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . '_categories c ON w.category_id = c.id
        WHERE w.assigned_to = ' . $user_id . ' AND w.is_deleted = 0 
        AND w.status IN ("pending", "doing", "review")
        ORDER BY w.due_date ASC, w.id DESC';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $row['due_date_formatted'] = $row['due_date'] > 0 ? workman_due_date_relative($row['due_date']) : '';
        $row['is_overdue'] = ($row['due_date'] > 0 && $row['due_date'] < NV_CURRENTTIME);
        
        // Phân loại theo status và giới hạn 5 items mỗi loại
        $status = $row['status'];
        if (isset($all_tasks[$status]) && count($all_tasks[$status]) < 5) {
            $all_tasks[$status][] = $row;
        }
    }
} catch (Exception $e) {
    // ignore
}

$pending_tasks = $all_tasks['pending'];
$doing_tasks = $all_tasks['doing'];
$review_tasks = $all_tasks['review'];

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
        $row['time_ago'] = workman_time_ago($row['created_at']);
        $notifications[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

$unread_count = count($notifications);

// ============================================================================
// Lấy deadline sắp tới (trong 3 ngày)
// ============================================================================
$upcoming_deadlines = [];
$three_days_later = NV_CURRENTTIME + (3 * 24 * 60 * 60);
$sql = 'SELECT w.*, c.title as category_title, c.color as category_color
        FROM ' . $db_config['prefix'] . '_' . $module_data . ' w
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . '_categories c ON w.category_id = c.id
        WHERE w.assigned_to = ' . $user_id . ' AND w.is_deleted = 0 
        AND w.status IN ("pending", "doing")
        AND w.due_date > 0 AND w.due_date <= ' . $three_days_later . '
        ORDER BY w.due_date ASC
        LIMIT 5';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $row['due_date_formatted'] = nv_date('d/m/Y H:i', $row['due_date']);
        $row['is_overdue'] = ($row['due_date'] < NV_CURRENTTIME);
        $row['is_today'] = (date('Y-m-d', $row['due_date']) == date('Y-m-d', NV_CURRENTTIME));
        $row['days_left'] = max(0, floor(($row['due_date'] - NV_CURRENTTIME) / 86400));
        $upcoming_deadlines[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Lấy hoạt động gần đây
// ============================================================================
$recent_activities = [];
$sql = 'SELECT l.*, w.title as work_title, u.username, u.first_name, u.last_name
        FROM ' . $db_config['prefix'] . '_' . $module_data . '_logs l
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . ' w ON l.work_id = w.id
        LEFT JOIN ' . $db_config['prefix'] . '_users u ON l.user_id = u.userid
        WHERE w.assigned_to = ' . $user_id . ' OR w.created_by = ' . $user_id . '
        ORDER BY l.created_at DESC
        LIMIT 5';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
        $row['user_fullname'] = !empty($fullname) ? $fullname : $row['username'];
        $row['created_at_formatted'] = nv_date('d/m H:i', $row['created_at']);
        $row['time_ago'] = workman_time_ago($row['created_at']);
        $recent_activities[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Render template
// ============================================================================
$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);

$xtpl->assign('LANG', $nv_Lang->getModule());
$xtpl->assign('GLANG', $nv_Lang->getGlobal());
$xtpl->assign('STATS', $stats);
$xtpl->assign('USER_NAME', $user_info['username']);
$xtpl->assign('UNREAD_COUNT', $unread_count);
$xtpl->assign('PROGRESS_PERCENT', $progress_percent);
$xtpl->assign('TOTAL_TASKS', $total_tasks);

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
    // Format message để hiển thị tên công việc thay vì ID
    if (!empty($notif['work_title'])) {
        $notif['message'] = preg_replace('/:\s*\d+$/', ': ' . $notif['work_title'], $notif['message']);
    }
    $xtpl->assign('NOTIF', $notif);
    $xtpl->parse('main.notification');
}

if (empty($notifications)) {
    $xtpl->parse('main.no_notifications');
}

// Upcoming deadlines
foreach ($upcoming_deadlines as $deadline) {
    $deadline['url_detail'] = $url_detail_base . $deadline['id'];
    $xtpl->assign('DEADLINE', $deadline);
    if ($deadline['is_overdue']) {
        $xtpl->parse('main.deadline.overdue');
    } elseif ($deadline['is_today']) {
        $xtpl->parse('main.deadline.today');
    }
    $xtpl->parse('main.deadline');
}

if (empty($upcoming_deadlines)) {
    $xtpl->parse('main.no_deadlines');
}

// Recent activities
foreach ($recent_activities as $activity) {
    $activity['url_detail'] = $url_detail_base . $activity['work_id'];
    $activity['action_text'] = $nv_Lang->getModule('log_' . $activity['action']) ?: $activity['action'];
    $xtpl->assign('ACTIVITY', $activity);
    $xtpl->parse('main.activity');
}

if (empty($recent_activities)) {
    $xtpl->parse('main.no_activities');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
