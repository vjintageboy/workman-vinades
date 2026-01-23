<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */
if (!defined('NV_IS_WORKMAN_ADMIN')) {
    exit('Stop!!!');
}

$page_title = $lang_module['main'];

// Thiết lập đường dẫn Template
$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['admin_theme'] . '/modules/' . $module_name);

// Dữ liệu mẫu (Sau này thay bằng query database)
$rows = [
    [
        'id' => 1,
        'title' => 'Thiết kế database',
        'description' => 'Thiết kế bảng công việc',
        'status' => 'done',
        'priority' => 'urgent', // Đã đổi để test màu
        'due_date' => time() - 3600
    ],
    [
        'id' => 2,
        'title' => 'Viết giao diện admin',
        'description' => 'XTemplate cho admin',
        'status' => 'doing',
        'priority' => 'normal',
        'due_date' => time() + 7200
    ]
];

foreach ($rows as $row) {

    // 1. Xử lý Trạng thái (Text và Class)
    $st_text_key = 'status_' . $row['status'];
    $st_class_key = 'status_class_' . $row['status'];
    
    $status_text = (isset($lang_module[$st_text_key])) ? $lang_module[$st_text_key] : $row['status'];
    $status_class = (isset($lang_module[$st_class_key])) ? $lang_module[$st_class_key] : 'label-default';
    
    // 2. Xử lý Mức độ ưu tiên (Text và Class)
    $pr_text_key = 'priority_' . $row['priority'];
    $pr_class_key = 'priority_class_' . $row['priority'];

    $priority_text = (isset($lang_module[$pr_text_key])) ? $lang_module[$pr_text_key] : $row['priority'];
    $priority_class = (isset($lang_module[$pr_class_key])) ? $lang_module[$pr_class_key] : 'info';
    
    // 3. Tạo link sửa
    $url_edit = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=edit&amp;id=" . $row['id'];
    
    $xtpl->assign('ROW', [
        'id' => $row['id'],
        'title' => $row['title'],
        'description' => nv_clean60($row['description'], 100),
        'status_text' => $status_text,
        'status_class' => $status_class,
        'priority_text' => $priority_text,
        'priority_class' => $priority_class,
        'due_date' => nv_date('d/m/Y H:i', $row['due_date']),
        'url_edit' => $url_edit,
    ]);

    $xtpl->parse('main.row'); 
}

// Đường dẫn link xóa
$url_delete = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=delete";
$xtpl->assign('URL_DELETE', $url_delete);

$xtpl->parse('main');
$contents = $xtpl->text('main');


include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
