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

// Xử lý xoá
if ($nv_Request->isset_request('delete_id', 'get')) {
    $id = $nv_Request->get_int('id', 'get', 0);
    if ($id > 0) {
        try {
            $sql = 'DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . ' WHERE id=' . $id;
            $db->exec($sql);
            $nv_Cache->delMod($module_name);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
        }
    }
}

// Thiết lập đường dẫn Template
$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['admin_theme'] . '/modules/' . $module_name);

// Lấy dữ liệu từ database
try {
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . ' ORDER BY id DESC';
    $result = $db->query($sql);
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}

while ($row = $result->fetch()) {

    // 1. Xử lý Trạng thái (Text và Class)
    $st_text_key = 'status_' . $row['status'];
    $st_class_key = 'status_class_' . $row['status'];
    
    $status_text_lang = $nv_Lang->getModule($st_text_key);
    $status_text = !empty($status_text_lang) ? $status_text_lang : $row['status'];

    $status_class_lang = $nv_Lang->getModule($st_class_key);
    $status_class = !empty($status_class_lang) ? $status_class_lang : 'default';
    
    // 2. Xử lý Mức độ ưu tiên (Text và Class)
    $pr_text_key = 'priority_' . $row['priority'];
    $pr_class_key = 'priority_class_' . $row['priority'];

    $priority_text_lang = $nv_Lang->getModule($pr_text_key);
    $priority_text = !empty($priority_text_lang) ? $priority_text_lang : $row['priority'];

    $priority_class_lang = $nv_Lang->getModule($pr_class_key);
    $priority_class = !empty($priority_class_lang) ? $priority_class_lang : 'info';
    
    // 3. Tạo link sửa và link xóa
    $base_url = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name;
    $url_edit = $base_url . "&amp;" . NV_OP_VARIABLE . "=add&amp;id=" . $row['id'];
    $url_delete = $base_url . "&amp;" . NV_OP_VARIABLE . "=main&amp;delete_id=1&amp;id=" . $row['id'];
    
    $xtpl->assign('ROW', [
        'id' => $row['id'],
        'title' => $row['title'],
        'description' => nv_clean60($row['description'], 100),
        'status_text' => $status_text,
        'status_class' => $status_class,
        'priority_text' => $priority_text,
        'priority_class' => $priority_class,
        'due_date' => $row['due_date'],
        'url_edit' => $url_edit,
        'url_delete' => $url_delete,
    ]);

    $xtpl->parse('main.row'); 
}

// Đường dẫn link xóa
// Assign additional variables
$url_add = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=add";

$xtpl->assign('LANG', $nv_Lang->getGlobal());
$xtpl->assign('URL_ADD', $url_add);

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';