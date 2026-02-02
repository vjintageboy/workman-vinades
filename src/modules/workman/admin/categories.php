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

$page_title = $nv_Lang->getModule('categories');

// ============================================================================
// Xử lý xóa category
// ============================================================================
if ($nv_Request->isset_request('delete_id', 'get')) {
    $delete_id = $nv_Request->get_int('delete_id', 'get', 0);
    if ($delete_id > 0) {
        // Kiểm tra có task nào thuộc category này không
        $sql = 'SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_' . $module_data . ' WHERE category_id = ' . $delete_id . ' AND is_deleted = 0';
        $count = $db->query($sql)->fetchColumn();
        
        if ($count > 0) {
            // Có tasks, không cho xóa - redirect với thông báo lỗi
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=categories&error=has_tasks');
        } else {
            $sql = 'DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_categories WHERE id = ' . $delete_id;
            $db->exec($sql);
            $nv_Cache->delMod($module_name);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=categories');
        }
    }
}

// ============================================================================
// Xử lý cập nhật thứ tự (weight) - AJAX
// ============================================================================
if ($nv_Request->isset_request('update_weight', 'post')) {
    $cat_id = $nv_Request->get_int('cat_id', 'post', 0);
    $new_weight = $nv_Request->get_int('weight', 'post', 0);
    
    if ($cat_id > 0) {
        $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_categories SET weight = ' . $new_weight . ' WHERE id = ' . $cat_id;
        $db->exec($sql);
        $nv_Cache->delMod($module_name);
    }
    
    die(json_encode(['success' => true]));
}

// ============================================================================
// Xử lý form thêm/sửa
// ============================================================================
$edit_id = $nv_Request->get_int('edit_id', 'get', 0);
$form_data = [
    'id' => 0,
    'title' => '',
    'description' => '',
    'color' => '#3498db',
    'weight' => 0,
    'status' => 1
];

// Load dữ liệu nếu đang sửa
if ($edit_id > 0) {
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_categories WHERE id = ' . $edit_id;
    $row = $db->query($sql)->fetch();
    if ($row) {
        $form_data = $row;
    }
}

$error = '';
$success = '';

// Kiểm tra lỗi từ redirect
if ($nv_Request->get_string('error', 'get', '') == 'has_tasks') {
    $error = $nv_Lang->getModule('error_category_has_tasks');
}

// Xử lý submit form
if ($nv_Request->get_int('submit', 'post') == 1) {
    $form_data['title'] = $nv_Request->get_string('title', 'post', '');
    $form_data['description'] = $nv_Request->get_string('description', 'post', '');
    $form_data['color'] = $nv_Request->get_string('color', 'post', '#3498db');
    $form_data['weight'] = $nv_Request->get_int('weight', 'post', 0);
    $form_data['status'] = $nv_Request->get_int('status', 'post', 1);
    $form_data['id'] = $nv_Request->get_int('id', 'post', 0);
    
    // Validation
    if (empty($form_data['title'])) {
        $error = $nv_Lang->getModule('error_required_title');
    } elseif (!preg_match('/^#[0-9A-Fa-f]{6}$/', $form_data['color'])) {
        $form_data['color'] = '#3498db'; // Reset về mặc định nếu color không hợp lệ
    }
    
    if (empty($error)) {
        try {
            if ($form_data['id'] > 0) {
                // UPDATE
                $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_categories SET 
                    title = ' . $db->quote($form_data['title']) . ',
                    description = ' . $db->quote($form_data['description']) . ',
                    color = ' . $db->quote($form_data['color']) . ',
                    weight = ' . intval($form_data['weight']) . ',
                    status = ' . intval($form_data['status']) . '
                    WHERE id = ' . intval($form_data['id']);
            } else {
                // INSERT
                $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_categories 
                    (title, description, color, weight, status) VALUES (
                    ' . $db->quote($form_data['title']) . ',
                    ' . $db->quote($form_data['description']) . ',
                    ' . $db->quote($form_data['color']) . ',
                    ' . intval($form_data['weight']) . ',
                    ' . intval($form_data['status']) . '
                )';
            }
            
            $db->exec($sql);
            $nv_Cache->delMod($module_name);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=categories');
            
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// ============================================================================
// Lấy danh sách categories
// ============================================================================
$categories = [];
$sql = 'SELECT c.*, (SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_' . $module_data . ' w WHERE w.category_id = c.id AND w.is_deleted = 0) as task_count 
        FROM ' . $db_config['prefix'] . '_' . $module_data . '_categories c
        ORDER BY c.weight ASC, c.title ASC';
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $categories[] = $row;
}

// ============================================================================
// Render template
// ============================================================================
$xtpl = new XTemplate('categories.tpl', NV_ROOTDIR . '/themes/' . $global_config['admin_theme'] . '/modules/' . $module_name);

$xtpl->assign('LANG', $nv_Lang->getModule());
$xtpl->assign('GLANG', $nv_Lang->getGlobal());
$xtpl->assign('ERROR', $error);
$xtpl->assign('SUCCESS', $success);
$xtpl->assign('FORM_DATA', $form_data);

// URLs
$url_back = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name;
$form_action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=categories';

$xtpl->assign('URL_BACK', $url_back);
$xtpl->assign('FORM_ACTION', $form_action);

// Parse form mode (add or edit)
if ($form_data['id'] > 0) {
    $xtpl->parse('main.form_edit');
    $xtpl->parse('main.btn_cancel');
} else {
    $xtpl->parse('main.form_add');
}

// Parse status select
if ($form_data['status'] == 1) {
    $xtpl->parse('main.status_active');
} else {
    $xtpl->parse('main.status_inactive');
}

// Render danh sách categories
if (empty($categories)) {
    $xtpl->parse('main.no_data');
} else {
    foreach ($categories as $cat) {
        $url_edit = $form_action . '&edit_id=' . $cat['id'];
        $url_delete = $form_action . '&delete_id=' . $cat['id'];
        
        $xtpl->assign('CAT', [
            'id' => $cat['id'],
            'title' => $cat['title'],
            'description' => $cat['description'],
            'color' => $cat['color'],
            'weight' => $cat['weight'],
            'status' => $cat['status'],
            'status_text' => $cat['status'] ? $nv_Lang->getGlobal('yes') : $nv_Lang->getGlobal('no'),
            'status_class' => $cat['status'] ? 'success' : 'default',
            'task_count' => $cat['task_count'],
            'url_edit' => $url_edit,
            'url_delete' => $url_delete
        ]);
        
        $xtpl->parse('main.category_row');
    }
}

if (!empty($error)) {
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
