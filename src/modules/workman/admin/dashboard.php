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

$page_title = $nv_Lang->getModule('dashboard');

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
// Thống kê theo Priority (NEW)
// ============================================================================
$stats_by_priority = [
    'low' => 0,
    'normal' => 0,
    'high' => 0,
    'urgent' => 0
];
$sql = 'SELECT priority, COUNT(*) as count FROM ' . $db_config['prefix'] . '_' . $module_data . ' 
        WHERE is_deleted = 0 AND status NOT IN ("done", "cancelled") 
        GROUP BY priority';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        if (isset($stats_by_priority[$row['priority']])) {
            $stats_by_priority[$row['priority']] = intval($row['count']);
        }
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Công việc sắp đến hạn - 7 ngày tới (NEW)
// ============================================================================
$upcoming_tasks = [];
$next_week = NV_CURRENTTIME + (7 * 86400);
$sql = 'SELECT w.*, c.title as category_title, c.color as category_color, u.username, u.first_name, u.last_name
        FROM ' . $db_config['prefix'] . '_' . $module_data . ' w
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . '_categories c ON w.category_id = c.id
        LEFT JOIN ' . $db_config['prefix'] . '_users u ON w.assigned_to = u.userid
        WHERE w.is_deleted = 0 
        AND w.due_date > ' . NV_CURRENTTIME . ' 
        AND w.due_date <= ' . $next_week . '
        AND w.status NOT IN ("done", "cancelled")
        ORDER BY w.due_date ASC
        LIMIT 10';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $row['due_date_formatted'] = nv_date('d/m/Y', $row['due_date']);
        $row['days_remaining'] = ceil(($row['due_date'] - NV_CURRENTTIME) / 86400);
        $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
        $row['assigned_name'] = !empty($fullname) ? $fullname : ($row['username'] ?: 'Chưa giao');
        $upcoming_tasks[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Tỷ lệ hoàn thành (NEW)
// ============================================================================
$completion_rate = 0;
$active_tasks = $stats['total'] - $stats['cancelled'];
if ($active_tasks > 0) {
    $completion_rate = round(($stats['done'] / $active_tasks) * 100, 1);
}

// ============================================================================
// Render template
// ============================================================================
$tpl = new \NukeViet\Template\NVSmarty();
$tpl->setTemplateDir(get_module_tpl_dir('dashboard.tpl'));

$tpl->assign('LANG', $nv_Lang);
$tpl->assign('GLANG', $nv_Lang);
$tpl->assign('MODULE_NAME', $module_name);

// Stats cards
$tpl->assign('STATS', $stats);

// Chart data for Status (Pie/Doughnut)
$chart_data = [
    'labels' => [$nv_Lang->getModule('status_draft'), $nv_Lang->getModule('status_pending'), 
                 $nv_Lang->getModule('status_doing'), $nv_Lang->getModule('status_review'),
                 $nv_Lang->getModule('status_done'), $nv_Lang->getModule('status_cancelled')],
    'data' => [$stats['draft'], $stats['pending'], $stats['doing'], $stats['review'], $stats['done'], $stats['cancelled']],
    'colors' => ['#6c757d', '#17a2b8', '#ffc107', '#007bff', '#28a745', '#dc3545']
];
$tpl->assign('CHART_DATA', json_encode($chart_data));

// Chart data for Priority (Bar chart) - NEW
$priority_chart_data = [
    'labels' => [$nv_Lang->getModule('priority_low'), $nv_Lang->getModule('priority_normal'), 
                 $nv_Lang->getModule('priority_high'), $nv_Lang->getModule('priority_urgent')],
    'data' => [$stats_by_priority['low'], $stats_by_priority['normal'], $stats_by_priority['high'], $stats_by_priority['urgent']],
    'colors' => ['#6c757d', '#17a2b8', '#ffc107', '#dc3545']
];
$tpl->assign('PRIORITY_CHART_DATA', json_encode($priority_chart_data));

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

// Upcoming tasks - add edit URLs (NEW)
foreach ($upcoming_tasks as &$task) {
    $task['url_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=add&amp;id=' . $task['id'];
}
unset($task);
$tpl->assign('UPCOMING_TASKS', $upcoming_tasks);

// Recent activities
$tpl->assign('RECENT_ACTIVITIES', $recent_activities);

// Completion rate (NEW)
$tpl->assign('COMPLETION_RATE', $completion_rate);
$tpl->assign('ACTIVE_TASKS', $active_tasks);
$tpl->assign('DONE_TASKS', $stats['done']);

// URLs
$url_back = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name;
$tpl->assign('URL_BACK', $url_back);

$contents = $tpl->fetch('dashboard.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';

