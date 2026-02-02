<?php

/**
 * NukeViet Content Management System - Workman Module
 * User Task List (Frontend)
 * @version 5.x
 */

if (!defined('NV_IS_MOD_WORKMAN')) {
    exit('Stop!!!');
}

// Kiểm tra user đã đăng nhập
if (!defined('NV_IS_USER')) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt(NV_MY_DOMAIN . NV_REQUEST_URI));
}

$page_title = $nv_Lang->getModule('my_tasks');

// Load helper functions
require_once NV_ROOTDIR . '/modules/' . $module_file . '/functions.php';

$user_id = $user_info['userid'];

// Lấy dữ liệu cho filters
$status_list = workman_get_status_list();

// Filter
$filter_status = $nv_Request->get_string('status', 'get', '');

// Build WHERE clause
$where_conditions = ['w.is_deleted = 0', 'w.assigned_to = ' . $user_id];
if (!empty($filter_status) && isset($status_list[$filter_status])) {
    $where_conditions[] = 'w.status = ' . $db->quote($filter_status);
}
$where_clause = implode(' AND ', $where_conditions);

// Phân trang
$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 10;

$base_url_params = [
    NV_LANG_VARIABLE . '=' . NV_LANG_DATA,
    NV_NAME_VARIABLE . '=' . $module_name,
    NV_OP_VARIABLE . '=list'
];
if (!empty($filter_status)) $base_url_params[] = 'status=' . $filter_status;
$base_url = NV_BASE_SITEURL . 'index.php?' . implode('&', $base_url_params);

// Đếm tổng số
$sql = 'SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_' . $module_data . ' w WHERE ' . $where_clause;
$num_items = $db->query($sql)->fetchColumn();

// Lấy dữ liệu
$tasks = [];
$offset = ($page - 1) * $per_page;
$sql = 'SELECT w.*, c.title as category_title, c.color as category_color
        FROM ' . $db_config['prefix'] . '_' . $module_data . ' w
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . '_categories c ON w.category_id = c.id
        WHERE ' . $where_clause . '
        ORDER BY 
            CASE w.status 
                WHEN "pending" THEN 1 
                WHEN "doing" THEN 2 
                WHEN "review" THEN 3 
                WHEN "done" THEN 4 
                ELSE 5 
            END,
            w.due_date ASC, w.id DESC
        LIMIT ' . $per_page . ' OFFSET ' . $offset;

try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $row['due_date_formatted'] = $row['due_date'] > 0 ? nv_date('d/m/Y', $row['due_date']) : '';
        $row['is_overdue'] = ($row['due_date'] > 0 && $row['due_date'] < NV_CURRENTTIME && !in_array($row['status'], ['done', 'cancelled']));
        $row['status_text'] = $nv_Lang->getModule('status_' . $row['status']) ?: $row['status'];
        $row['status_class'] = $nv_Lang->getModule('status_class_' . $row['status']) ?: 'secondary';
        $row['priority_text'] = $nv_Lang->getModule('priority_' . $row['priority']) ?: $row['priority'];
        $row['priority_class'] = $nv_Lang->getModule('priority_class_' . $row['priority']) ?: 'info';
        $tasks[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);

// ============================================================================
// Render template
// ============================================================================
$xtpl = new XTemplate('list.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);

$xtpl->assign('LANG', $nv_Lang->getModule());
$xtpl->assign('GLANG', $nv_Lang->getGlobal());
$xtpl->assign('FILTER_STATUS', $filter_status);
$xtpl->assign('TOTAL', $num_items);

// Status options for filter
foreach ($status_list as $key => $label) {
    $xtpl->assign('STATUS_OPTION', [
        'key' => $key,
        'label' => $label,
        'selected' => ($filter_status == $key) ? 'selected' : ''
    ]);
    $xtpl->parse('main.status_option');
}

// URLs
$url_dashboard = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name;
$url_detail_base = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=detail&id=';
$form_action = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=list';

$xtpl->assign('URL_DASHBOARD', $url_dashboard);
$xtpl->assign('FORM_ACTION', $form_action);

// Tasks
foreach ($tasks as $task) {
    $task['url_detail'] = $url_detail_base . $task['id'];
    $xtpl->assign('TASK', $task);
    
    if ($task['is_overdue']) {
        $xtpl->parse('main.task.overdue');
    }
    
    $xtpl->parse('main.task');
}

if (empty($tasks)) {
    $xtpl->parse('main.no_tasks');
}

// Pagination
if (!empty($generate_page)) {
    $xtpl->assign('GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.pagination');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
