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

// DEBUG: Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $page_title = $nv_Lang->getModule('add');
} catch (Exception $e) {
    die('Error getting page title: ' . $e->getMessage());
}

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

// Khởi tạo data
$request_data = [
    'title' => '',
    'description' => '',
    'status' => 'doing',
    'priority' => 'normal',
    'due_date' => date('d/m/Y H:i'),
    'attachment' => ''
];

$id = $nv_Request->get_int('id', 'get', 0);
if ($id > 0) {
    $page_title = $nv_Lang->getModule('edit');
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . ' WHERE id=' . $id;
    
    // DEBUG: In câu SQL
    // echo "SQL: " . $sql . "<br>";
    
    try {
        $row = $db->query($sql)->fetch();
        if ($row) {
            $request_data = $row;
            // DEBUG: In dữ liệu
            // echo "Data loaded: "; print_r($request_data); echo "<br>";
        }
    } catch (Exception $e) {
        die('Database error when fetching: ' . $e->getMessage() . '<br>SQL: ' . $sql);
    }
}

$error = '';

// Xử lý form submit
if ($nv_Request->get_int('submit', 'post') == 1) {
    $request_data['title'] = $nv_Request->get_string('title', 'post', '');
    $request_data['description'] = $nv_Request->get_textarea('description', '', 'post');
    $request_data['status'] = $nv_Request->get_string('status', 'post', 'doing');
    $request_data['priority'] = $nv_Request->get_string('priority', 'post', 'normal');
    $request_data['due_date'] = $nv_Request->get_string('due_date', 'post', '');
    
    // Giữ lại file cũ nếu đang edit
    $old_attachment = $request_data['attachment'];

    if (empty($request_data['title'])) {
        $error = $nv_Lang->getModule('error_required_title');
    } else {
        // Ưu tiên 1: Upload ảnh
        if (isset($_FILES['attachment_image']) && is_uploaded_file($_FILES['attachment_image']['tmp_name'])) {
            $upload = new NukeViet\Files\Upload(
                ['images'], // Chỉ cho phép ảnh
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
                
                // Xử lý ảnh: resize và tối ưu
                try {
                    $image = new NukeViet\Files\Image(
                        $upload_info['name'], 
                        1920, 
                        1920
                    );
                    $image->resizeXY(1920, 1920);
                    $image->save(NV_WORKMAN_UPLOAD_REAL_DIR, $upload_info['basename'], 85);
                    $image->close();
                } catch (Exception $e) {
                    // Bỏ qua lỗi xử lý ảnh
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
                ['documents', 'archives', 'adobe'], // Không bao gồm ảnh
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

        if (empty($error)) {
            try {
                if ($id > 0) {
                    $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . ' SET 
                        title=' . $db->quote($request_data['title']) . ', 
                        description=' . $db->quote($request_data['description']) . ', 
                        status=' . $db->quote($request_data['status']) . ', 
                        priority=' . $db->quote($request_data['priority']) . ', 
                        due_date=' . $db->quote($request_data['due_date']) . ',
                        attachment=' . $db->quote($request_data['attachment']) . '
                        WHERE id=' . $id;
                } else {
                    $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . ' 
                        (title, description, status, priority, due_date, attachment) VALUES (
                        ' . $db->quote($request_data['title']) . ',
                        ' . $db->quote($request_data['description']) . ',
                        ' . $db->quote($request_data['status']) . ',
                        ' . $db->quote($request_data['priority']) . ',
                        ' . $db->quote($request_data['due_date']) . ',
                        ' . $db->quote($request_data['attachment']) . '
                    )';
                }

                $ex = $db->exec($sql);
                
                if ($ex == 1 || ($id > 0 && $ex >= 0)) {
                    $nv_Cache->delMod($module_name);
                    nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
                } else {
                    $error = 'Error saving data: Execute returned ' . $ex;
                }
            } catch (Exception $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// DEBUG: Kiểm tra dữ liệu trước khi render
// echo "<pre>Request Data: "; print_r($request_data); echo "</pre>";

// Khởi tạo Smarty
try {
    $xtpl = new \NukeViet\Template\NVSmarty();
    $xtpl->setTemplateDir(get_module_tpl_dir('add.tpl'));

    // Assign dữ liệu
    $xtpl->assign('LANG', \NukeViet\Core\Language::$lang_module); 
    $xtpl->assign('GLANG', \NukeViet\Core\Language::$lang_global);
    $xtpl->assign('TITLE', $page_title);
    
    // Thêm tên file để hiển thị
    if (!empty($request_data['attachment'])) {
        $request_data['attachment_name'] = basename($request_data['attachment']);
        // Kiểm tra xem có phải là ảnh không
        $request_data['is_image'] = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $request_data['attachment']);
    } else {
        $request_data['attachment_name'] = '';
        $request_data['is_image'] = false;
    }
    
    $xtpl->assign('DATA', $request_data);
    $xtpl->assign('ERROR', $error);

    // URL back
    $url_back = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name;
    $xtpl->assign('URL_BACK', $url_back);

    // Danh sách trạng thái và ưu tiên
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
} catch (Exception $e) {
    die('Template rendering error: ' . $e->getMessage() . '<br>Trace: ' . $e->getTraceAsString());
}