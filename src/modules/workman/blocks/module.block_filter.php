<?php

/**
 * NukeViet Content Management System - Workman Module
 * Block: Task Filter (Bộ lọc công việc)
 * @version 5.x
 */

if (!defined('NV_IS_MOD_WORKMAN')) {
    exit('Stop!!!');
}

global $db, $db_config, $module_data, $module_name, $module_file, $global_config, $user_info, $nv_Lang, $nv_Request;

// Load helper functions nếu chưa load
if (!function_exists('workman_get_status_list')) {
    require_once NV_ROOTDIR . '/modules/' . $module_file . '/functions.php';
}

// Kiểm tra user đã đăng nhập
if (!defined('NV_IS_USER')) {
    return;
}

$user_id = $user_info['userid'];

// Lấy filter hiện tại từ URL
$filter_status = $nv_Request->get_string('status', 'get', '');
$filter_category = $nv_Request->get_int('category', 'get', 0);
$filter_priority = $nv_Request->get_string('priority', 'get', '');

// Lấy danh sách status
$status_list = workman_get_status_list();

// Lấy danh sách priority
$priority_list = workman_get_priority_list();

// Lấy danh sách categories
$categories = workman_get_categories();

// Đếm số task theo status
$stats = workman_count_tasks_by_status($user_id);

// Base URL cho filter
$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=list';

// Template
[$template, $dir] = get_module_tpl_dir('block.filter.tpl', true);
$xtpl = new XTemplate('block.filter.tpl', $dir);

$xtpl->assign('LANG', $nv_Lang->getModule());
$xtpl->assign('BASE_URL', $base_url);
$xtpl->assign('FILTER_STATUS', $filter_status);
$xtpl->assign('FILTER_CATEGORY', $filter_category);
$xtpl->assign('FILTER_PRIORITY', $filter_priority);

// Status items
$status_icons = [
    'draft' => 'fa-pencil',
    'pending' => 'fa-hourglass-half',
    'doing' => 'fa-cogs',
    'review' => 'fa-eye',
    'done' => 'fa-check-circle',
    'cancelled' => 'fa-times-circle'
];

$status_colors = [
    'draft' => '#95a5a6',
    'pending' => '#3498db',
    'doing' => '#e67e22',
    'review' => '#9b59b6',
    'done' => '#27ae60',
    'cancelled' => '#e74c3c'
];

// All tasks
$xtpl->assign('STATUS_ITEM', [
    'key' => '',
    'label' => $nv_Lang->getModule('all'),
    'count' => $stats['total'],
    'icon' => 'fa-list',
    'color' => '#34495e',
    'active' => empty($filter_status) ? 'active' : '',
    'url' => $base_url
]);
$xtpl->parse('main.status_item');

foreach ($status_list as $key => $label) {
    $count = isset($stats[$key]) ? $stats[$key] : 0;
    $url_params = ['status=' . $key];
    if ($filter_category > 0) $url_params[] = 'category=' . $filter_category;
    if (!empty($filter_priority)) $url_params[] = 'priority=' . $filter_priority;
    
    $xtpl->assign('STATUS_ITEM', [
        'key' => $key,
        'label' => $label,
        'count' => $count,
        'icon' => isset($status_icons[$key]) ? $status_icons[$key] : 'fa-circle',
        'color' => isset($status_colors[$key]) ? $status_colors[$key] : '#999',
        'active' => ($filter_status === $key) ? 'active' : '',
        'url' => $base_url . '&' . implode('&', $url_params)
    ]);
    $xtpl->parse('main.status_item');
}

// Category items
if (!empty($categories)) {
    foreach ($categories as $cat) {
        $url_params = ['category=' . $cat['id']];
        if (!empty($filter_status)) $url_params[] = 'status=' . $filter_status;
        if (!empty($filter_priority)) $url_params[] = 'priority=' . $filter_priority;
        
        $xtpl->assign('CATEGORY_ITEM', [
            'id' => $cat['id'],
            'title' => $cat['title'],
            'color' => $cat['color'],
            'active' => ($filter_category == $cat['id']) ? 'active' : '',
            'url' => $base_url . '&' . implode('&', $url_params)
        ]);
        $xtpl->parse('main.category_section.category_item');
    }
    
    // Clear category filter link
    $clear_cat_params = [];
    if (!empty($filter_status)) $clear_cat_params[] = 'status=' . $filter_status;
    if (!empty($filter_priority)) $clear_cat_params[] = 'priority=' . $filter_priority;
    $xtpl->assign('CLEAR_CATEGORY_URL', $base_url . (!empty($clear_cat_params) ? '&' . implode('&', $clear_cat_params) : ''));
    $xtpl->assign('CATEGORY_ALL_ACTIVE', ($filter_category == 0) ? 'active' : '');
    
    $xtpl->parse('main.category_section');
}

// Priority items
$priority_icons = [
    'low' => 'fa-arrow-down',
    'normal' => 'fa-minus',
    'high' => 'fa-arrow-up',
    'urgent' => 'fa-exclamation'
];

$priority_colors = [
    'low' => '#95a5a6',
    'normal' => '#3498db',
    'high' => '#e67e22',
    'urgent' => '#e74c3c'
];

foreach ($priority_list as $key => $label) {
    $url_params = ['priority=' . $key];
    if (!empty($filter_status)) $url_params[] = 'status=' . $filter_status;
    if ($filter_category > 0) $url_params[] = 'category=' . $filter_category;
    
    $xtpl->assign('PRIORITY_ITEM', [
        'key' => $key,
        'label' => $label,
        'icon' => isset($priority_icons[$key]) ? $priority_icons[$key] : 'fa-flag',
        'color' => isset($priority_colors[$key]) ? $priority_colors[$key] : '#999',
        'active' => ($filter_priority === $key) ? 'active' : '',
        'url' => $base_url . '&' . implode('&', $url_params)
    ]);
    $xtpl->parse('main.priority_item');
}

// Clear all filters
$xtpl->assign('CLEAR_ALL_URL', $base_url);

$xtpl->parse('main');
$content = $xtpl->text('main');
