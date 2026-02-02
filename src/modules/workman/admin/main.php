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

$page_title = $nv_Lang->getModule('main');

// Helper functions are loaded automatically from admin.functions.php

// ============================================================================
// Xử lý Bulk Actions
// ============================================================================
if ($nv_Request->isset_request('bulk_action', 'post')) {
    $action = $nv_Request->get_string('bulk_action', 'post', '');
    $selected_ids = $nv_Request->get_typed_array('selected_ids', 'post', 'int', []);
    
    if (!empty($selected_ids) && !empty($action)) {
        $ids_string = implode(',', array_filter($selected_ids));
        
        try {
            if ($action == 'delete' && !empty($ids_string)) {
                // Soft delete
                $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . ' 
                        SET is_deleted = 1, deleted_at = ' . NV_CURRENTTIME . ' 
                        WHERE id IN (' . $ids_string . ')';
                $db->exec($sql);
                
                // Log activity for each deleted task
                foreach ($selected_ids as $task_id) {
                    if ($task_id > 0) {
                        workman_log_activity($task_id, 'deleted', '', 'Xóa hàng loạt');
                    }
                }
                
                $nv_Cache->delMod($module_name);
            } elseif (in_array($action, ['draft', 'pending', 'doing', 'review', 'done', 'cancelled']) && !empty($ids_string)) {
                // Change status
                $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . ' 
                        SET status = ' . $db->quote($action) . ', updated_at = ' . NV_CURRENTTIME . '
                        WHERE id IN (' . $ids_string . ')';
                $db->exec($sql);
                
                // Log activity for each task
                $status_labels = workman_get_status_list();
                $status_label = isset($status_labels[$action]) ? $status_labels[$action] : $action;
                foreach ($selected_ids as $task_id) {
                    if ($task_id > 0) {
                        workman_log_activity($task_id, 'status_changed', '', $status_label);
                    }
                }
                
                $nv_Cache->delMod($module_name);
            }
            
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
            
        } catch (Exception $e) {
            trigger_error($e->getMessage());
        }
    }
}

// ============================================================================
// Xử lý xóa đơn lẻ
// ============================================================================
if ($nv_Request->isset_request('delete_id', 'get')) {
    $id = $nv_Request->get_int('id', 'get', 0);
    if ($id > 0) {
        try {
            // Soft delete
            $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . ' 
                    SET is_deleted = 1, deleted_at = ' . NV_CURRENTTIME . ' WHERE id=' . $id;
            $db->exec($sql);
            workman_log_activity($id, 'deleted');
            $nv_Cache->delMod($module_name);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
        }
    }
}

// ============================================================================
// Lấy dữ liệu cho filters
// ============================================================================
$categories = workman_get_categories();
$users = workman_get_users();
$status_list = workman_get_status_list();
$priority_list = workman_get_priority_list();

// ============================================================================
// Xử lý Filters
// ============================================================================
$filter_status = $nv_Request->get_string('filter_status', 'get', '');
$filter_category = $nv_Request->get_int('filter_category', 'get', 0);
$filter_assigned = $nv_Request->get_int('filter_assigned', 'get', 0);
$filter_priority = $nv_Request->get_string('filter_priority', 'get', '');

// Build WHERE clause
$where_conditions = ['w.is_deleted = 0'];

if (!empty($filter_status) && isset($status_list[$filter_status])) {
    $where_conditions[] = 'w.status = ' . $db->quote($filter_status);
}
if ($filter_category > 0) {
    $where_conditions[] = 'w.category_id = ' . $filter_category;
}
if ($filter_assigned > 0) {
    $where_conditions[] = 'w.assigned_to = ' . $filter_assigned;
}
if (!empty($filter_priority) && isset($priority_list[$filter_priority])) {
    $where_conditions[] = 'w.priority = ' . $db->quote($filter_priority);
}

$where_clause = implode(' AND ', $where_conditions);

// ============================================================================
// Thiết lập đường dẫn Template
// ============================================================================
$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['admin_theme'] . '/modules/' . $module_name);

// ============================================================================
// Phân trang
// ============================================================================
$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 20;

// Build base URL with filters
$base_url_params = [
    NV_LANG_VARIABLE . '=' . NV_LANG_DATA,
    NV_NAME_VARIABLE . '=' . $module_name,
    NV_OP_VARIABLE . '=main'
];
if (!empty($filter_status)) $base_url_params[] = 'filter_status=' . $filter_status;
if ($filter_category > 0) $base_url_params[] = 'filter_category=' . $filter_category;
if ($filter_assigned > 0) $base_url_params[] = 'filter_assigned=' . $filter_assigned;
if (!empty($filter_priority)) $base_url_params[] = 'filter_priority=' . $filter_priority;

$base_url = NV_BASE_ADMINURL . 'index.php?' . implode('&', $base_url_params);

// Đếm tổng số bản ghi
$sql = 'SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_' . $module_data . ' w WHERE ' . $where_clause;
$num_items = $db->query($sql)->fetchColumn();

// ============================================================================
// Lấy dữ liệu từ database
// ============================================================================
try {
    $offset = ($page - 1) * $per_page;
    $sql = 'SELECT w.*, c.title as category_title, c.color as category_color
            FROM ' . $db_config['prefix'] . '_' . $module_data . ' w
            LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . '_categories c ON w.category_id = c.id
            WHERE ' . $where_clause . '
            ORDER BY w.id DESC 
            LIMIT ' . $per_page . ' OFFSET ' . $offset;
    $result = $db->query($sql);
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}

$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);

// ============================================================================
// Render danh sách
// ============================================================================
$row_count = 0;
while ($row = $result->fetch()) {
    $row_count++;
    // Status
    $st_text_key = 'status_' . $row['status'];
    $st_class_key = 'status_class_' . $row['status'];
    $status_text = $nv_Lang->getModule($st_text_key) ?: $row['status'];
    $status_class = $nv_Lang->getModule($st_class_key) ?: 'secondary';
    
    // Priority
    $pr_text_key = 'priority_' . $row['priority'];
    $pr_class_key = 'priority_class_' . $row['priority'];
    $priority_text = $nv_Lang->getModule($pr_text_key) ?: $row['priority'];
    $priority_class = $nv_Lang->getModule($pr_class_key) ?: 'info';
    
    // Category
    $category_title = $row['category_title'] ?: $nv_Lang->getModule('no_category');
    $category_color = $row['category_color'] ?: '#999999';
    
    // Assigned user
    $assigned_name = '';
    if ($row['assigned_to'] > 0 && isset($users[$row['assigned_to']])) {
        $assigned_name = $users[$row['assigned_to']]['fullname'];
    }
    
    // Due date formatting
    $due_date_text = '';
    $due_date_class = '';
    if ($row['due_date'] > 0) {
        $due_date_text = nv_date('d/m/Y', $row['due_date']);
        if ($row['due_date'] < NV_CURRENTTIME && $row['status'] != 'done') {
            $due_date_class = 'text-danger';
        } elseif ($row['due_date'] < NV_CURRENTTIME + 86400 && $row['status'] != 'done') {
            $due_date_class = 'text-warning';
        }
    }
    
    // Attachment
    $attachment_icon = '';
    $attachment_url = '';
    $attachment_name = '';
    if (!empty($row['attachment'])) {
        $attachment_name = basename($row['attachment']);
        $attachment_url = NV_BASE_SITEURL . $row['attachment'];
        $file_ext = strtolower(pathinfo($attachment_name, PATHINFO_EXTENSION));
        $icon_map = [
            'pdf' => 'fa-file-pdf-o', 'doc' => 'fa-file-word-o', 'docx' => 'fa-file-word-o',
            'xls' => 'fa-file-excel-o', 'xlsx' => 'fa-file-excel-o',
            'jpg' => 'fa-file-image-o', 'jpeg' => 'fa-file-image-o', 'png' => 'fa-file-image-o', 'gif' => 'fa-file-image-o'
        ];
        $attachment_icon = isset($icon_map[$file_ext]) ? $icon_map[$file_ext] : 'fa-file-o';
    }
    
    // URLs
    $url_detail = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=detail&amp;id=' . $row['id'];
    $url_edit = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=add&amp;id=' . $row['id'];
    $url_delete = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=main&amp;delete_id=1&amp;id=' . $row['id'];
    
    $xtpl->assign('ROW', [
        'id' => $row['id'],
        'title' => $row['title'],
        'description' => nv_clean60($row['description'], 80),
        'status_text' => $status_text,
        'status_class' => $status_class,
        'priority_text' => $priority_text,
        'priority_class' => $priority_class,
        'category_title' => $category_title,
        'category_color' => $category_color,
        'assigned_name' => $assigned_name,
        'due_date' => $due_date_text,
        'due_date_class' => $due_date_class,
        'attachment_icon' => $attachment_icon,
        'attachment_url' => $attachment_url,
        'attachment_name' => $attachment_name,
        'url_detail' => $url_detail,
        'url_edit' => $url_edit,
        'url_delete' => $url_delete,
    ]);

    // Parse attachment block
    if (!empty($row['attachment'])) {
        $xtpl->parse('main.row.attachment');
    }

    $xtpl->parse('main.row'); 
}

// Parse no_data if no rows
if ($row_count == 0) {
    $xtpl->parse('main.no_data');
}

// ============================================================================
// Assign variables for filters
// ============================================================================
$xtpl->assign('LANG', $nv_Lang->getModule());
$xtpl->assign('GLANG', $nv_Lang->getGlobal());

// Filter dropdowns
$xtpl->assign('FILTER_STATUS', $filter_status);
$xtpl->assign('FILTER_CATEGORY', $filter_category);
$xtpl->assign('FILTER_ASSIGNED', $filter_assigned);
$xtpl->assign('FILTER_PRIORITY', $filter_priority);

// Status options
foreach ($status_list as $key => $label) {
    $xtpl->assign('STATUS_OPTION', [
        'key' => $key,
        'label' => $label,
        'selected' => ($filter_status == $key) ? 'selected' : ''
    ]);
    $xtpl->parse('main.filter_form.status_option');
}

// Category options
foreach ($categories as $cat) {
    $xtpl->assign('CATEGORY_OPTION', [
        'id' => $cat['id'],
        'title' => $cat['title'],
        'selected' => ($filter_category == $cat['id']) ? 'selected' : ''
    ]);
    $xtpl->parse('main.filter_form.category_option');
}

// User options
foreach ($users as $user) {
    $xtpl->assign('USER_OPTION', [
        'userid' => $user['userid'],
        'fullname' => $user['fullname'],
        'selected' => ($filter_assigned == $user['userid']) ? 'selected' : ''
    ]);
    $xtpl->parse('main.filter_form.user_option');
}

// Priority options
foreach ($priority_list as $key => $label) {
    $xtpl->assign('PRIORITY_OPTION', [
        'key' => $key,
        'label' => $label,
        'selected' => ($filter_priority == $key) ? 'selected' : ''
    ]);
    $xtpl->parse('main.filter_form.priority_option');
}

$xtpl->parse('main.filter_form');

// URLs
$url_add = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=add';
$url_categories = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=categories';
$url_reports = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=reports';

$xtpl->assign('URL_ADD', $url_add);
$xtpl->assign('URL_CATEGORIES', $url_categories);
$xtpl->assign('URL_REPORTS', $url_reports);
$xtpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);

// Status options for bulk action
foreach ($status_list as $key => $label) {
    $xtpl->assign('BULK_STATUS', [
        'key' => $key,
        'label' => $label
    ]);
    $xtpl->parse('main.bulk_status_option');
}

// Pagination
if (!empty($generate_page)) {
    $xtpl->assign('GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.generate_page');
}

// Statistics
$stats = workman_count_tasks_by_status();
$xtpl->assign('STATS', $stats);

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';