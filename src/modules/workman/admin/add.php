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

// Helper functions are loaded automatically from admin.functions.php

$page_title = $nv_Lang->getModule('add');

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

// Lấy dữ liệu cho dropdowns
$categories = workman_get_categories();
$users = workman_get_users();
$status_list = workman_get_status_list();
$priority_list = workman_get_priority_list();

// Khởi tạo data mặc định
$request_data = [
    'id' => 0,
    'title' => '',
    'description' => '',
    'status' => 'draft',
    'priority' => 'normal',
    'due_date' => date('d/m/Y H:i', NV_CURRENTTIME + 7 * 86400), // Default: 7 ngày sau
    'attachment' => '',
    'category_id' => 0,
    'assigned_to' => 0,
    'created_by' => 0,
    'created_at' => 0,
    'updated_at' => 0
];

$id = $nv_Request->get_int('id', 'get', 0);
$is_edit = false;

if ($id > 0) {
    $is_edit = true;
    $page_title = $nv_Lang->getModule('edit');
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . ' WHERE id=' . $id . ' AND is_deleted = 0';
    
    try {
        $row = $db->query($sql)->fetch();
        if ($row) {
            $request_data = $row;
            // Format due_date for display
            if ($request_data['due_date'] > 0) {
                $request_data['due_date'] = nv_date('d/m/Y H:i', $request_data['due_date']);
            } else {
                $request_data['due_date'] = '';
            }
        } else {
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
        }
    } catch (Exception $e) {
        die('Database error: ' . $e->getMessage());
    }
}

$error = '';
$success = '';

// ============================================================================
// Xử lý form submit
// ============================================================================
if ($nv_Request->get_int('submit', 'post') == 1) {
    $request_data['title'] = $nv_Request->get_string('title', 'post', '');
    $request_data['description'] = $nv_Request->get_textarea('description', '', 'post');
    $request_data['status'] = $nv_Request->get_string('status', 'post', 'draft');
    $request_data['priority'] = $nv_Request->get_string('priority', 'post', 'normal');
    $request_data['category_id'] = $nv_Request->get_int('category_id', 'post', 0);
    $request_data['assigned_to'] = $nv_Request->get_int('assigned_to', 'post', 0);
    $due_date_string = $nv_Request->get_string('due_date', 'post', '');
    
    // Parse due_date từ format d/m/Y H:i thành timestamp
    $due_date_timestamp = 0;
    if (!empty($due_date_string)) {
        $date_parts = date_parse_from_format('d/m/Y H:i', $due_date_string);
        if ($date_parts['error_count'] == 0) {
            $due_date_timestamp = mktime(
                $date_parts['hour'], $date_parts['minute'], 0,
                $date_parts['month'], $date_parts['day'], $date_parts['year']
            );
        }
    }
    
    // Giữ lại file cũ nếu đang edit
    $old_attachment = isset($request_data['attachment']) ? $request_data['attachment'] : '';
    $old_status = $is_edit ? $row['status'] : '';
    $old_assigned_to = $is_edit ? $row['assigned_to'] : 0;

    // Validation
    if (empty($request_data['title'])) {
        $error = $nv_Lang->getModule('error_required_title');
    } elseif (!isset($status_list[$request_data['status']])) {
        $error = $nv_Lang->getModule('error_invalid_status');
    } else {
        // ============================================================================
        // Xử lý Upload file
        // ============================================================================
        // Ưu tiên 1: Upload ảnh
        if (isset($_FILES['attachment_image']) && is_uploaded_file($_FILES['attachment_image']['tmp_name'])) {
            $upload = new NukeViet\Files\Upload(
                ['images'],
                $global_config['forbid_extensions'], 
                $global_config['forbid_mimes'], 
                NV_UPLOAD_MAX_FILESIZE, 
                NV_MAX_WIDTH, 
                NV_MAX_HEIGHT
            );
            $upload->setLanguage(\NukeViet\Core\Language::$lang_global);
            $upload_info = $upload->save_file(
                $_FILES['attachment_image'], 
                NV_WORKMAN_UPLOAD_REAL_DIR, 
                false,
                $global_config['nv_auto_resize']
            );
            @unlink($_FILES['attachment_image']['tmp_name']);
            
            if (!empty($upload_info['error'])) {
                $error = $upload_info['error'];
            } else {
                @chmod($upload_info['name'], 0644);
                $request_data['attachment'] = NV_WORKMAN_UPLOAD_DIR . '/' . $upload_info['basename'];
                
                // Xử lý ảnh: resize
                try {
                    $image = new NukeViet\Files\Image($upload_info['name'], 1920, 1920);
                    $image->resizeXY(1920, 1920);
                    $image->save(NV_WORKMAN_UPLOAD_REAL_DIR, $upload_info['basename'], 85);
                    $image->close();
                } catch (Exception $e) {
                    // Ignore image processing errors
                }
                
                // Xóa file cũ
                if ($old_attachment && $old_attachment != $request_data['attachment']) {
                    @unlink(NV_ROOTDIR . '/' . $old_attachment);
                }
            }
        }
        // Ưu tiên 2: Upload file tài liệu
        elseif (isset($_FILES['attachment']) && is_uploaded_file($_FILES['attachment']['tmp_name'])) {
            $upload = new NukeViet\Files\Upload(
                ['documents', 'archives', 'adobe'],
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
            
            if (!empty($upload_info['error'])) {
                $error = $upload_info['error'];
            } else {
                @chmod($upload_info['name'], 0644);
                $request_data['attachment'] = NV_WORKMAN_UPLOAD_DIR . '/' . $upload_info['basename'];
                
                // Xóa file cũ
                if ($old_attachment && $old_attachment != $request_data['attachment']) {
                    @unlink(NV_ROOTDIR . '/' . $old_attachment);
                }
            }
        }
        // Ưu tiên 3: Giữ file cũ
        else {
            $request_data['attachment'] = $old_attachment;
        }

        // ============================================================================
        // Save to database
        // ============================================================================
        if (empty($error)) {
            try {
                if ($is_edit) {
                    // UPDATE
                    $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . ' SET 
                        title = ' . $db->quote($request_data['title']) . ', 
                        description = ' . $db->quote($request_data['description']) . ', 
                        status = ' . $db->quote($request_data['status']) . ', 
                        priority = ' . $db->quote($request_data['priority']) . ', 
                        due_date = ' . intval($due_date_timestamp) . ',
                        attachment = ' . $db->quote($request_data['attachment']) . ',
                        category_id = ' . intval($request_data['category_id']) . ',
                        assigned_to = ' . intval($request_data['assigned_to']) . ',
                        updated_at = ' . NV_CURRENTTIME . '
                        WHERE id = ' . $id;
                    
                    // Nếu status = done, cập nhật completed_at
                    if ($request_data['status'] == 'done' && $old_status != 'done') {
                        $sql = str_replace('updated_at = ' . NV_CURRENTTIME, 
                            'updated_at = ' . NV_CURRENTTIME . ', completed_at = ' . NV_CURRENTTIME, $sql);
                    }
                    
                    $db->exec($sql);
                    
                    // Log activity
                    if ($old_status != $request_data['status']) {
                        workman_log_activity($id, 'status_changed', $old_status, $request_data['status']);
                    } else {
                        workman_log_activity($id, 'updated');
                    }
                    
                    // Notify nếu assign user mới
                    if ($old_assigned_to != $request_data['assigned_to'] && $request_data['assigned_to'] > 0) {
                        workman_log_activity($id, 'assigned', '', $request_data['assigned_to']);
                        $notify_msg = sprintf($nv_Lang->getModule('notification_assigned'), $request_data['title']);
                        workman_notify($request_data['assigned_to'], $id, 'assigned', $notify_msg);
                    }
                    
                } else {
                    // INSERT
                    $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . ' 
                        (title, description, status, priority, due_date, attachment, category_id, assigned_to, created_by, created_at, updated_at) VALUES (
                        ' . $db->quote($request_data['title']) . ',
                        ' . $db->quote($request_data['description']) . ',
                        ' . $db->quote($request_data['status']) . ',
                        ' . $db->quote($request_data['priority']) . ',
                        ' . intval($due_date_timestamp) . ',
                        ' . $db->quote($request_data['attachment']) . ',
                        ' . intval($request_data['category_id']) . ',
                        ' . intval($request_data['assigned_to']) . ',
                        ' . intval($admin_info['admin_id']) . ',
                        ' . NV_CURRENTTIME . ',
                        ' . NV_CURRENTTIME . '
                    )';
                    
                    $db->exec($sql);
                    $new_id = $db->lastInsertId();
                    
                    // Log activity
                    workman_log_activity($new_id, 'created');
                    
                    // Notify nếu assign user
                    if ($request_data['assigned_to'] > 0) {
                        workman_log_activity($new_id, 'assigned', '', $request_data['assigned_to']);
                        $notify_msg = sprintf($nv_Lang->getModule('notification_assigned'), $request_data['title']);
                        workman_notify($request_data['assigned_to'], $new_id, 'assigned', $notify_msg);
                    }
                }

                $nv_Cache->delMod($module_name);
                nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
                
            } catch (Exception $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    // Restore due_date display format khi có lỗi
    $request_data['due_date'] = $due_date_string;
}

// ============================================================================
// Render template
// ============================================================================

try {
    $xtpl = new \NukeViet\Template\NVSmarty();
    // Explicitly use admin theme directory for admin module templates
    $tpl_dir = NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_name;
    $xtpl->setTemplateDir($tpl_dir);
    echo $global_config['module_theme'];

    // Assign dữ liệu
    $xtpl->assign('LANG', \NukeViet\Core\Language::$lang_module); 
    $xtpl->assign('GLANG', \NukeViet\Core\Language::$lang_global);
    $xtpl->assign('TITLE', $page_title);
    
    // Thêm thông tin file để hiển thị
    if (!empty($request_data['attachment'])) {
        $request_data['attachment_name'] = basename($request_data['attachment']);
        $request_data['is_image'] = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $request_data['attachment']);
    } else {
        $request_data['attachment_name'] = '';
        $request_data['is_image'] = false;
    }
    
    $xtpl->assign('DATA', $request_data);
    $xtpl->assign('ERROR', $error);
    $xtpl->assign('SUCCESS', $success);
    $xtpl->assign('IS_EDIT', $is_edit);

    // URL back
    $url_back = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name;
    $xtpl->assign('URL_BACK', $url_back);

    // Danh sách status, priority, categories, users
    $xtpl->assign('STATUS_LIST', $status_list);
    $xtpl->assign('PRIORITY_LIST', $priority_list);
    $xtpl->assign('CATEGORIES', $categories);
    $xtpl->assign('USERS', $users);

    // Render template
    $contents = $xtpl->fetch('add.tpl');

    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
} catch (Exception $e) {
    die('Template rendering error: ' . $e->getMessage());
}