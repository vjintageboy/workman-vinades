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

$page_title = $nv_Lang->getModule('task_detail');

$id = $nv_Request->get_int('id', 'get', 0);

if ($id <= 0) {
    nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

// ============================================================================
// Xử lý POST comment từ admin
// ============================================================================
if ($nv_Request->isset_request('submit_comment', 'post')) {
    $content = $nv_Request->get_editor('content', '', NV_ALLOWED_HTML_TAGS);
    
    if (!empty(trim($content))) {
        $attachment = '';
        
        // Định nghĩa thư mục upload
        if (!defined('NV_WORKMAN_UPLOAD_DIR')) {
            define('NV_WORKMAN_UPLOAD_DIR', NV_UPLOADS_DIR . '/workman');
        }
        if (!defined('NV_WORKMAN_UPLOAD_REAL_DIR')) {
            define('NV_WORKMAN_UPLOAD_REAL_DIR', NV_UPLOADS_REAL_DIR . '/workman');
        }
        
        // Tạo thư mục nếu chưa có
        if (!is_dir(NV_WORKMAN_UPLOAD_REAL_DIR)) {
            nv_mkdir(NV_ROOTDIR . '/' . NV_WORKMAN_UPLOAD_DIR, NV_WORKMAN_UPLOAD_DIR);
        }
        
        // Xử lý file upload
        if (isset($_FILES['attachment']) && is_uploaded_file($_FILES['attachment']['tmp_name'])) {
            $upload = new NukeViet\Files\Upload(
                ['images', 'documents', 'archives', 'adobe'],
                $global_config['forbid_extensions'], 
                $global_config['forbid_mimes'], 
                NV_UPLOAD_MAX_FILESIZE, 
                NV_MAX_WIDTH, 
                NV_MAX_HEIGHT
            );
            $upload->setLanguage(\NukeViet\Core\Language::$lang_global);
            $upload_info = $upload->save_file(
                $_FILES['attachment'], 
                NV_WORKMAN_UPLOAD_REAL_DIR, 
                false,
                $global_config['nv_auto_resize']
            );
            @unlink($_FILES['attachment']['tmp_name']);
            
            if (empty($upload_info['error'])) {
                @chmod($upload_info['name'], 0644);
                $attachment = NV_WORKMAN_UPLOAD_DIR . '/' . $upload_info['basename'];
            }
        }
        
        // Insert comment
        try {
            $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_comments 
                    (work_id, user_id, content, attachment, created_at) VALUES (
                    ' . $id . ',
                    ' . $admin_info['admin_id'] . ',
                    ' . $db->quote($content) . ',
                    ' . $db->quote($attachment) . ',
                    ' . NV_CURRENTTIME . '
                )';
            $db->exec($sql);
            
            // Log activity
            workman_log_activity($id, 'commented');
            
            // Lấy thông tin task để notify user được assign
            $sql = 'SELECT assigned_to, title FROM ' . $db_config['prefix'] . '_' . $module_data . ' WHERE id = ' . $id;
            $task = $db->query($sql)->fetch();
            
            if ($task && $task['assigned_to'] > 0 && $task['assigned_to'] != $admin_info['admin_id']) {
                $notify_msg = sprintf($nv_Lang->getModule('notification_admin_commented'), $task['title']);
                workman_notify($task['assigned_to'], $id, 'commented', $notify_msg);
            }
            
            $nv_Cache->delMod($module_name);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=detail&id=' . $id);
            
        } catch (Exception $e) {
            trigger_error('Comment error: ' . $e->getMessage());
        }
    }
}

// ============================================================================
// Lấy thông tin task
// ============================================================================
$sql = 'SELECT w.*, c.title as category_title, c.color as category_color,
        u1.username as creator_username, u1.first_name as creator_first_name, u1.last_name as creator_last_name,
        u2.username as assigned_username, u2.first_name as assigned_first_name, u2.last_name as assigned_last_name
        FROM ' . $db_config['prefix'] . '_' . $module_data . ' w
        LEFT JOIN ' . $db_config['prefix'] . '_' . $module_data . '_categories c ON w.category_id = c.id
        LEFT JOIN ' . $db_config['prefix'] . '_users u1 ON w.created_by = u1.userid
        LEFT JOIN ' . $db_config['prefix'] . '_users u2 ON w.assigned_to = u2.userid
        WHERE w.id = ' . $id;

$task = $db->query($sql)->fetch();

if (!$task) {
    nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

$page_title = $task['title'];

// Format data
$task['due_date_formatted'] = $task['due_date'] > 0 ? nv_date('d/m/Y H:i', $task['due_date']) : '';
$task['created_at_formatted'] = $task['created_at'] > 0 ? nv_date('d/m/Y H:i', $task['created_at']) : '';
$task['updated_at_formatted'] = $task['updated_at'] > 0 ? nv_date('d/m/Y H:i', $task['updated_at']) : '';
$task['completed_at_formatted'] = $task['completed_at'] > 0 ? nv_date('d/m/Y H:i', $task['completed_at']) : '';
$task['is_overdue'] = ($task['due_date'] > 0 && $task['due_date'] < NV_CURRENTTIME && !in_array($task['status'], ['done', 'cancelled']));
$task['status_text'] = $nv_Lang->getModule('status_' . $task['status']) ?: $task['status'];
$task['status_class'] = $nv_Lang->getModule('status_class_' . $task['status']) ?: 'secondary';
$task['priority_text'] = $nv_Lang->getModule('priority_' . $task['priority']) ?: $task['priority'];
$task['priority_class'] = $nv_Lang->getModule('priority_class_' . $task['priority']) ?: 'info';

$creator_fullname = trim($task['creator_first_name'] . ' ' . $task['creator_last_name']);
$task['creator_name'] = !empty($creator_fullname) ? $creator_fullname : $task['creator_username'];

$assigned_fullname = trim($task['assigned_first_name'] . ' ' . $task['assigned_last_name']);
$task['assigned_name'] = !empty($assigned_fullname) ? $assigned_fullname : $task['assigned_username'];

// Comment count
$sql = 'SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_' . $module_data . '_comments WHERE work_id = ' . $id;
$task['comment_count'] = $db->query($sql)->fetchColumn();

// Attachment
if (!empty($task['attachment'])) {
    $task['attachment_name'] = basename($task['attachment']);
    $task['attachment_url'] = NV_BASE_SITEURL . $task['attachment'];
    $task['is_image'] = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $task['attachment']);
}

// ============================================================================
// Lấy comments
// ============================================================================
$comments = [];
$sql = 'SELECT cm.*, u.username, u.first_name, u.last_name
        FROM ' . $db_config['prefix'] . '_' . $module_data . '_comments cm
        LEFT JOIN ' . $db_config['prefix'] . '_users u ON cm.user_id = u.userid
        WHERE cm.work_id = ' . $id . '
        ORDER BY cm.created_at ASC';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
        $row['user_fullname'] = !empty($fullname) ? $fullname : $row['username'];
        $row['created_at_formatted'] = nv_date('d/m/Y H:i', $row['created_at']);
        $row['is_admin'] = ($row['user_id'] == $task['created_by']); // Kiểm tra xem có phải admin tạo task không
        if (!empty($row['attachment'])) {
            $row['attachment_name'] = basename($row['attachment']);
            $row['attachment_url'] = NV_BASE_SITEURL . $row['attachment'];
        }
        $comments[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Lấy activity logs
// ============================================================================
$logs = [];
$sql = 'SELECT l.*, u.username, u.first_name, u.last_name
        FROM ' . $db_config['prefix'] . '_' . $module_data . '_logs l
        LEFT JOIN ' . $db_config['prefix'] . '_users u ON l.user_id = u.userid
        WHERE l.work_id = ' . $id . '
        ORDER BY l.created_at DESC
        LIMIT 30';
try {
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
        $row['user_fullname'] = !empty($fullname) ? $fullname : $row['username'];
        $row['created_at_formatted'] = nv_date('d/m/Y H:i', $row['created_at']);
        $row['action_text'] = $nv_Lang->getModule('log_' . $row['action']) ?: $row['action'];
        $logs[] = $row;
    }
} catch (Exception $e) {
    // ignore
}

// ============================================================================
// Render template
// ============================================================================
$tpl = new \NukeViet\Template\NVSmarty();
$tpl->setTemplateDir(get_module_tpl_dir('detail.tpl'));

$tpl->assign('LANG', $nv_Lang);
$tpl->assign('GLANG', $nv_Lang);
$tpl->assign('MODULE_NAME', $module_name);
$tpl->assign('TASK', $task);
$tpl->assign('TASK_ID', $id);

// URLs
$url_back = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name;
$url_edit = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=add&id=' . $id;
$form_action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=detail&id=' . $id;
$url_comment = $form_action;

$tpl->assign('URL_BACK', $url_back);
$tpl->assign('URL_EDIT', $url_edit);
$tpl->assign('FORM_ACTION', $form_action);
$tpl->assign('URL_COMMENT', $url_comment);

// Comments
$tpl->assign('COMMENTS', $comments);

// Activity logs
$tpl->assign('LOGS', $logs);

// Status list for quick change
$status_list = workman_get_status_list();
$tpl->assign('STATUS_LIST', $status_list);

// Priority list for quick change
$priority_list = workman_get_priority_list();
$tpl->assign('PRIORITY_LIST', $priority_list);

$contents = $tpl->fetch('detail.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';

