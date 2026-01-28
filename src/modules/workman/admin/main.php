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

// Phân trang
// Phân trang
$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 3;
$base_url = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=main";

// Đếm tổng số bản ghi
$sql = 'SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_' . $module_data;
$num_items = $db->query($sql)->fetchColumn();

// Lấy dữ liệu từ database với LIMIT và OFFSET
try {
    $offset = ($page - 1) * $per_page;
    $sql = 'SELECT id, title, description, status, priority, due_date, attachment FROM ' . $db_config['prefix'] . '_' . $module_data . ' ORDER BY id DESC LIMIT ' . $per_page . ' OFFSET ' . $offset;
    $result = $db->query($sql);
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}

$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);

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
    
    // 3. Xử lý Attachment (File đính kèm)
    $attachment_icon = '';
    $attachment_url = '';
    $attachment_name = '';
    
    if (!empty($row['attachment'])) {
        $attachment_name = basename($row['attachment']);
        $attachment_url = NV_BASE_SITEURL . 'uploads/' . $module_name . '/' . $attachment_name;
        
        // Xác định icon dựa trên phần mở rộng của file
        $file_ext = strtolower(pathinfo($attachment_name, PATHINFO_EXTENSION));
        
        $icon_map = [
            'pdf' => 'fa-file-pdf-o',
            'doc' => 'fa-file-word-o',
            'docx' => 'fa-file-word-o',
            'xls' => 'fa-file-excel-o',
            'xlsx' => 'fa-file-excel-o',
            'ppt' => 'fa-file-powerpoint-o',
            'pptx' => 'fa-file-powerpoint-o',
            'zip' => 'fa-file-archive-o',
            'rar' => 'fa-file-archive-o',
            '7z' => 'fa-file-archive-o',
            'jpg' => 'fa-file-image-o',
            'jpeg' => 'fa-file-image-o',
            'png' => 'fa-file-image-o',
            'gif' => 'fa-file-image-o',
            'txt' => 'fa-file-text-o',
            'csv' => 'fa-file-text-o'
        ];
        
        $attachment_icon = isset($icon_map[$file_ext]) ? $icon_map[$file_ext] : 'fa-file-o';
    }
    
    // 4. Tạo link sửa và link xóa
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
        'attachment_icon' => $attachment_icon,
        'attachment_url' => $attachment_url,
        'attachment_name' => $attachment_name,
        'url_edit' => $url_edit,
        'url_delete' => $url_delete,
    ]);

    // Parse attachment block
    if (!empty($row['attachment'])) {
        $xtpl->parse('main.row.attachment');
    } else {
        $xtpl->parse('main.row.no_attachment');
    }

    $xtpl->parse('main.row'); 
}

// Đường dẫn link xóa
// Assign additional variables
$url_add = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=add";

$xtpl->assign('LANG', $nv_Lang->getGlobal());
$xtpl->assign('URL_ADD', $url_add);

if (!empty($generate_page)) {
    $xtpl->assign('GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.generate_page');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';