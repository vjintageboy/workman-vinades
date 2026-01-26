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

$page_title = $nv_Lang->getModule('add');
// echo $global_config['admin_theme'];
// Khởi tạo data
$request_data = [
    'title' => '',
    'description' => '',
    'status' => 'doing',
    'priority' => 'normal',
    'due_date' => date('d/m/Y H:i')
];

$error = '';

// Xử lý form submit
if ($nv_Request->get_int('submit', 'post') == 1) {
    $request_data['title'] = $nv_Request->get_string('title', 'post', '');
    $request_data['description'] = $nv_Request->get_textarea('description', '', 'post');
    $request_data['status'] = $nv_Request->get_string('status', 'post', 'doing');
    $request_data['priority'] = $nv_Request->get_string('priority', 'post', 'normal');
    $request_data['due_date'] = $nv_Request->get_string('due_date', 'post', '');

    if (empty($request_data['title'])) {
        $error = $nv_Lang->getModule('error_required_title');
    } else {
        $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . ' (title, description, status, priority, due_date) VALUES (
            ' . $db->quote($request_data['title']) . ',
            ' . $db->quote($request_data['description']) . ',
            ' . $db->quote($request_data['status']) . ',
            ' . $db->quote($request_data['priority']) . ',
            ' . $db->quote($request_data['due_date']) . '
        )';

        $ex = $db->exec($sql);
        if ($ex == 1) {
            $nv_Cache->delMod($module_name);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
        } else {
            $error = 'Error saving data';
        }
        die();
    }
}

// Khởi tạo Smarty
$xtpl = new \NukeViet\Template\NVSmarty();
$xtpl->setTemplateDir(get_module_tpl_dir('add.tpl'));

// Assign dữ liệu
$xtpl->assign('LANG', \NukeViet\Core\Language::$lang_module); 
$xtpl->assign('DATA', $request_data);
$xtpl->assign('ERROR', $error);
$xtpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=add');

// Danh sách trạng thái và ưu tiên dùng cho select box
$status_list = [
    'doing' => $nv_Lang->getModule('status_doing'),
    'done' => $nv_Lang->getModule('status_done')
];
$xtpl->assign('STATUS_LIST', $status_list);

$priority_list = [
    'normal' => $nv_Lang->getModule('priority_normal'),
    'urgent' => $nv_Lang->getModule('priority_urgent')
];
$xtpl->assign('PRIORITY_LIST', $priority_list);

// Render template
$contents = $xtpl->fetch('add.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';