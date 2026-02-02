<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */
if (!defined('NV_IS_FILE_ADMIN')) {
    exit('Stop!!!');
}

// Helper functions are loaded automatically from admin.functions.php

$page_title = $nv_Lang->getModule('reports');

// ============================================================================
// Lấy thống kê tổng quan
// ============================================================================
$stats = workman_count_tasks_by_status();

// ============================================================================
// Thống kê theo User
// ============================================================================
$stats_by_user = [];
$sql = 'SELECT w.assigned_to, u.username, u.first_name, u.last_name,
        COUNT(*) as total,
        SUM(CASE WHEN w.status = "pending" THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN w.status = "doing" THEN 1 ELSE 0 END) as doing,
        SUM(CASE WHEN w.status = "review" THEN 1 ELSE 0 END) as review,
        SUM(CASE WHEN w.status = "done" THEN 1 ELSE 0 END) as done
        FROM ' . $db_config['prefix'] . '_' . $module_data . ' w
        LEFT JOIN ' . $db_config['prefix'] . '_users u ON w.assigned_to = u.userid
        WHERE w.is_deleted = 0 AND w.assigned_to > 0
        GROUP BY w.assigned_to
        ORDER BY total DESC
        LIMIT 20';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
        $row['fullname'] = !empty($fullname) ? $fullname : $row['username'];
        $stats_by_user[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Thống kê theo Category
// ============================================================================
$stats_by_category = [];
$sql = 'SELECT c.id, c.title, c.color, COUNT(w.id) as total,
        SUM(CASE WHEN w.status = "pending" THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN w.status = "doing" THEN 1 ELSE 0 END) as doing,
        SUM(CASE WHEN w.status = "done" THEN 1 ELSE 0 END) as done
        FROM ' . $db_config['prefix'] . '_' . $module_data . '_categories c
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . ' w ON c.id = w.category_id AND w.is_deleted = 0
        GROUP BY c.id
        ORDER BY total DESC';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $stats_by_category[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Công việc quá hạn
// ============================================================================
$overdue_tasks = [];
$sql = 'SELECT w.*, c.title as category_title, c.color as category_color
        FROM ' . $db_config['prefix'] . '_' . $module_data . ' w
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . '_categories c ON w.category_id = c.id
        WHERE w.is_deleted = 0 
        AND w.due_date > 0 
        AND w.due_date < ' . NV_CURRENTTIME . '
        AND w.status NOT IN ("done", "cancelled")
        ORDER BY w.due_date ASC
        LIMIT 10';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $row['due_date_formatted'] = nv_date('d/m/Y', $row['due_date']);
        $row['days_overdue'] = floor((NV_CURRENTTIME - $row['due_date']) / 86400);
        $overdue_tasks[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Hoạt động gần đây
// ============================================================================
$recent_activities = [];
$sql = 'SELECT l.*, w.title as work_title, u.username, u.first_name, u.last_name
        FROM ' . $db_config['prefix'] . '_' . $module_data . '_logs l
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . ' w ON l.work_id = w.id
        LEFT JOIN ' . $db_config['prefix'] . '_users u ON l.user_id = u.userid
        ORDER BY l.created_at DESC
        LIMIT 20';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
        $row['user_fullname'] = !empty($fullname) ? $fullname : $row['username'];
        $row['created_at_formatted'] = nv_date('d/m/Y H:i', $row['created_at']);
        $row['action_text'] = $nv_Lang->getModule('log_' . $row['action']) ?: $row['action'];
        $recent_activities[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Render template
// ============================================================================
$tpl = new \NukeViet\Template\NVSmarty();
$tpl->setTemplateDir(get_module_tpl_dir('reports.tpl'));

$tpl->assign('LANG', $nv_Lang);
$tpl->assign('GLANG', $nv_Lang);
$tpl->assign('MODULE_NAME', $module_name);

// Stats cards
$tpl->assign('STATS', $stats);

// Chart data for JS (JSON)
$chart_data = [
    'labels' => [$nv_Lang->getModule('status_draft'), $nv_Lang->getModule('status_pending'), 
                 $nv_Lang->getModule('status_doing'), $nv_Lang->getModule('status_review'),
                 $nv_Lang->getModule('status_done'), $nv_Lang->getModule('status_cancelled')],
    'data' => [$stats['draft'], $stats['pending'], $stats['doing'], $stats['review'], $stats['done'], $stats['cancelled']],
    'colors' => ['#6c757d', '#17a2b8', '#ffc107', '#007bff', '#28a745', '#dc3545']
];
$tpl->assign('CHART_DATA', json_encode($chart_data));

// Stats by user
$tpl->assign('STATS_BY_USER', $stats_by_user);

// Stats by category
$tpl->assign('STATS_BY_CATEGORY', $stats_by_category);

// Overdue tasks - add edit URLs
foreach ($overdue_tasks as &$task) {
    $task['url_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=add&amp;id=' . $task['id'];
}
unset($task);
$tpl->assign('OVERDUE_TASKS', $overdue_tasks);

// Recent activities
$tpl->assign('RECENT_ACTIVITIES', $recent_activities);

// URLs
$url_back = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name;
$tpl->assign('URL_BACK', $url_back);

$contents = $tpl->fetch('reports.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';

