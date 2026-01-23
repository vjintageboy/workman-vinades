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
        // Thực hiện lưu dữ liệu vào database ở đây
        // ...
        // Redirect sau khi lưu thành công
        // nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
    }
}

// Khởi tạo Smarty
// Lưu ý: NukeViet có autoloader cho thư viện vendor
$smarty = new \Smarty\Smarty();
$smarty->setTemplateDir(NV_ROOTDIR . '/themes/' . $global_config['admin_theme'] . '/modules/' . $module_name);
$smarty->setCompileDir(NV_ROOTDIR . '/data/cache/smarty-compile');
$smarty->setCacheDir(NV_ROOTDIR . '/data/cache/smarty-cache');
$smarty->setConfigDir(NV_ROOTDIR . '/data/cache/smarty-config');

// Assign dữ liệu
$smarty->assign('LANG', \NukeViet\Core\Language::$lang_module); 
$smarty->assign('DATA', $request_data);
$smarty->assign('ERROR', $error);
$smarty->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=add');

// Danh sách trạng thái và ưu tiên dùng cho select box
$status_list = [
    'doing' => $nv_Lang->getModule('status_doing'),
    'done' => $nv_Lang->getModule('status_done')
];
$smarty->assign('STATUS_LIST', $status_list);

$priority_list = [
    'normal' => $nv_Lang->getModule('priority_normal'),
    'urgent' => $nv_Lang->getModule('priority_urgent')
];
$smarty->assign('PRIORITY_LIST', $priority_list);

// Render template
$contents = $smarty->fetch('add.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';