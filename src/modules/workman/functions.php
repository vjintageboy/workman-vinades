<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_SYSTEM')) {
    exit('Stop!!!');
}

define('NV_IS_WORKMAN_ADMIN', true);

// Load CSS for workman module (frontend)
global $my_head, $global_config, $module_info, $op_file;
$css_file = NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/css/workman.css';
if (file_exists($css_file)) {
    $my_head .= '<link rel="stylesheet" href="' . NV_STATIC_URL . 'themes/' . $global_config['module_theme'] . '/css/workman.css?v=' . time() . '">' . "\n";
}

// Override layout để sử dụng layout phù hợp cho từng function
// Chỉ áp dụng cho frontend
if (!defined('NV_ADMIN')) {
    global $op;
    
    // Layout mặc định là 'main' (full-width)
    foreach ($module_info['funcs'] as $func_name => $func_values) {
        $module_info['layout_funcs'][$func_name] = 'main';
    }
    
    // Trang list sử dụng layout 'left-main' để có sidebar filter
    $module_info['layout_funcs']['list'] = 'left-main';
    
    // Đảm bảo op hiện tại cũng dùng layout phù hợp
    if (!empty($op)) {
        if ($op === 'list') {
            $module_info['layout_funcs'][$op] = 'left-main';
        } else {
            $module_info['layout_funcs'][$op] = 'main';
        }
    }
}





/**
 * Lấy danh sách categories đang active
 * 
 * @return array
 */
function workman_get_categories()
{
    global $db, $db_config, $module_data;
    
    $categories = [];
    $sql = 'SELECT id, title, color, weight FROM ' . $db_config['prefix'] . '_' . $module_data . '_categories 
            WHERE status = 1 ORDER BY weight ASC, title ASC';
    
    try {
        $result = $db->query($sql);
        while ($row = $result->fetch()) {
            $categories[$row['id']] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'color' => $row['color'],
                'weight' => $row['weight']
            ];
        }
    } catch (Exception $e) {
        trigger_error('workman_get_categories: ' . $e->getMessage());
    }
    
    return $categories;
}

/**
 * Lấy danh sách users có thể assign công việc
 * 
 * @return array
 */
function workman_get_users()
{
    global $db, $db_config;
    
    $users = [];
    // Lấy users active từ bảng users của NukeViet
    $sql = 'SELECT userid, username, first_name, last_name FROM ' . $db_config['prefix'] . '_users 
            WHERE active = 1 ORDER BY username ASC LIMIT 100';
    
    try {
        $result = $db->query($sql);
        while ($row = $result->fetch()) {
            $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
            $users[$row['userid']] = [
                'userid' => $row['userid'],
                'username' => $row['username'],
                'fullname' => !empty($fullname) ? $fullname : $row['username']
            ];
        }
    } catch (Exception $e) {
        trigger_error('workman_get_users: ' . $e->getMessage());
    }
    
    return $users;
}

/**
 * Lấy thông tin user theo ID
 * 
 * @param int $user_id
 * @return array|false
 */
function workman_get_user_info($user_id)
{
    global $db, $db_config;
    
    $user_id = intval($user_id);
    if ($user_id <= 0) {
        return false;
    }
    
    $sql = 'SELECT userid, username, first_name, last_name FROM ' . $db_config['prefix'] . '_users WHERE userid = ' . $user_id;
    
    try {
        $row = $db->query($sql)->fetch();
        if ($row) {
            $fullname = trim($row['first_name'] . ' ' . $row['last_name']);
            return [
                'userid' => $row['userid'],
                'username' => $row['username'],
                'fullname' => !empty($fullname) ? $fullname : $row['username']
            ];
        }
    } catch (Exception $e) {
        trigger_error('workman_get_user_info: ' . $e->getMessage());
    }
    
    return false;
}

/**
 * Ghi log hoạt động
 * 
 * @param int $work_id ID công việc
 * @param string $action Hành động: created, updated, status_changed, assigned, commented, deleted
 * @param string $old_value Giá trị cũ
 * @param string $new_value Giá trị mới
 * @return bool
 */
function workman_log_activity($work_id, $action, $old_value = '', $new_value = '')
{
    global $db, $db_config, $module_data, $admin_info, $user_info;
    
    // Lấy user_id từ admin hoặc user đang đăng nhập
    $user_id = 0;
    if (!empty($admin_info['admin_id'])) {
        $user_id = $admin_info['admin_id'];
    } elseif (!empty($user_info['userid'])) {
        $user_id = $user_info['userid'];
    }
    
    $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_logs 
            (work_id, user_id, action, old_value, new_value, created_at) VALUES 
            (' . intval($work_id) . ', ' . intval($user_id) . ', ' . $db->quote($action) . ', 
             ' . $db->quote($old_value) . ', ' . $db->quote($new_value) . ', ' . NV_CURRENTTIME . ')';
    
    try {
        $db->exec($sql);
        return true;
    } catch (Exception $e) {
        trigger_error('workman_log_activity: ' . $e->getMessage());
        return false;
    }
}

/**
 * Tạo thông báo cho user
 * 
 * @param int $user_id ID người nhận
 * @param int $work_id ID công việc
 * @param string $type Loại: assigned, status_changed, commented, deadline_reminder
 * @param string $message Nội dung thông báo
 * @return bool
 */
function workman_notify($user_id, $work_id, $type, $message)
{
    global $db, $db_config, $module_data;
    
    if ($user_id <= 0) {
        return false;
    }
    
    $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_notifications 
            (user_id, work_id, type, message, is_read, created_at) VALUES 
            (' . intval($user_id) . ', ' . intval($work_id) . ', ' . $db->quote($type) . ', 
             ' . $db->quote($message) . ', 0, ' . NV_CURRENTTIME . ')';
    
    try {
        $db->exec($sql);
        return true;
    } catch (Exception $e) {
        trigger_error('workman_notify: ' . $e->getMessage());
        return false;
    }
}

/**
 * Lấy danh sách status với label
 * 
 * @return array
 */
function workman_get_status_list()
{
    global $nv_Lang;
    
    return [
        'draft' => $nv_Lang->getModule('status_draft'),
        'pending' => $nv_Lang->getModule('status_pending'),
        'doing' => $nv_Lang->getModule('status_doing'),
        'review' => $nv_Lang->getModule('status_review'),
        'done' => $nv_Lang->getModule('status_done'),
        'cancelled' => $nv_Lang->getModule('status_cancelled')
    ];
}

/**
 * Lấy danh sách priority với label
 * 
 * @return array
 */
function workman_get_priority_list()
{
    global $nv_Lang;
    
    return [
        'low' => $nv_Lang->getModule('priority_low'),
        'normal' => $nv_Lang->getModule('priority_normal'),
        'high' => $nv_Lang->getModule('priority_high'),
        'urgent' => $nv_Lang->getModule('priority_urgent')
    ];
}

/**
 * Validate chuyển đổi trạng thái theo workflow
 * 
 * @param string $old_status Trạng thái cũ
 * @param string $new_status Trạng thái mới
 * @param bool $is_admin Có phải admin không
 * @return bool
 */
function workman_validate_status_transition($old_status, $new_status, $is_admin = false)
{
    // Admin workflow
    $admin_transitions = [
        'draft' => ['pending', 'cancelled'],
        'pending' => ['doing', 'cancelled'],
        'doing' => ['review', 'cancelled'],
        'review' => ['done', 'doing'],
        'done' => [],
        'cancelled' => ['draft']
    ];
    
    // User workflow (chỉ được chuyển một số trạng thái)
    $user_transitions = [
        'pending' => ['doing'],
        'doing' => ['review'],
        'review' => [],
        'done' => [],
        'cancelled' => []
    ];
    
    $allowed = $is_admin ? $admin_transitions : $user_transitions;
    
    if (!isset($allowed[$old_status])) {
        return false;
    }
    
    return in_array($new_status, $allowed[$old_status]);
}

/**
 * Đếm số notifications chưa đọc của user
 * 
 * @param int $user_id
 * @return int
 */
function workman_count_unread_notifications($user_id)
{
    global $db, $db_config, $module_data;
    
    $sql = 'SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_' . $module_data . '_notifications 
            WHERE user_id = ' . intval($user_id) . ' AND is_read = 0';
    
    try {
        return intval($db->query($sql)->fetchColumn());
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Đánh dấu notification đã đọc
 * 
 * @param int $notification_id
 * @param int $user_id
 * @return bool
 */
function workman_mark_notification_read($notification_id, $user_id)
{
    global $db, $db_config, $module_data;
    
    $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_notifications 
            SET is_read = 1 
            WHERE id = ' . intval($notification_id) . ' AND user_id = ' . intval($user_id);
    
    try {
        $db->exec($sql);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Lấy thông tin category theo ID
 * 
 * @param int $category_id
 * @return array|false
 */
function workman_get_category_info($category_id)
{
    global $db, $db_config, $module_data;
    
    $category_id = intval($category_id);
    if ($category_id <= 0) {
        return false;
    }
    
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_categories WHERE id = ' . $category_id;
    
    try {
        $row = $db->query($sql)->fetch();
        return $row ?: false;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Đếm số task theo status của user
 * 
 * @param int $user_id
 * @return array
 */
function workman_count_tasks_by_status($user_id = 0)
{
    global $db, $db_config, $module_data;
    
    $counts = [
        'draft' => 0,
        'pending' => 0,
        'doing' => 0,
        'review' => 0,
        'done' => 0,
        'cancelled' => 0,
        'total' => 0
    ];
    
    $where = 'is_deleted = 0';
    if ($user_id > 0) {
        $where .= ' AND assigned_to = ' . intval($user_id);
    }
    
    $sql = 'SELECT status, COUNT(*) as cnt FROM ' . $db_config['prefix'] . '_' . $module_data . ' 
            WHERE ' . $where . ' GROUP BY status';
    
    try {
        $result = $db->query($sql);
        while ($row = $result->fetch()) {
            if (isset($counts[$row['status']])) {
                $counts[$row['status']] = intval($row['cnt']);
                $counts['total'] += intval($row['cnt']);
            }
        }
    } catch (Exception $e) {
        trigger_error('workman_count_tasks_by_status: ' . $e->getMessage());
    }
    
    return $counts;
}

/**
 * Trả về JSON response đúng chuẩn với HTTP header và status code
 * 
 * @param array $data Dữ liệu response
 * @param int $http_code HTTP status code (mặc định auto detect từ error field)
 * @return void
 */
function workman_json_response($data, $http_code = null)
{
    // Xóa output buffer nếu có (tránh lỗi do nội dung trước đó)
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Set Content-Type header
    header('Content-Type: application/json; charset=utf-8');
    
    // Tự động xác định HTTP status code nếu không được chỉ định
    if ($http_code === null) {
        $http_code = (!empty($data['error'])) ? 400 : 200;
    }
    
    // Set HTTP status code
    http_response_code($http_code);
    
    // Gửi response và exit
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Chuyển timestamp thành "time ago" format
 * 
 * @param int $timestamp Unix timestamp
 * @return string
 */
function workman_time_ago($timestamp)
{
    $diff = NV_CURRENTTIME - $timestamp;
    
    if ($diff < 60) {
        return 'Vừa xong';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' phút trước';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' giờ trước';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' ngày trước';
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . ' tuần trước';
    } else {
        return nv_date('d/m/Y', $timestamp);
    }
}

/**
 * Chuyển due date timestamp thành format tương đối
 * Hiển thị "Còn X ngày", "Hôm nay", "Quá hạn X ngày"
 * 
 * @param int $timestamp Unix timestamp của due date
 * @return string
 */
function workman_due_date_relative($timestamp)
{
    if ($timestamp <= 0) {
        return '';
    }
    
    $diff = $timestamp - NV_CURRENTTIME;
    $is_today = (date('Y-m-d', $timestamp) == date('Y-m-d', NV_CURRENTTIME));
    
    if ($is_today) {
        return 'Hôm nay';
    }
    
    if ($diff > 0) {
        // Tương lai
        if ($diff < 3600) {
            $mins = ceil($diff / 60);
            return 'Còn ' . $mins . ' phút';
        } elseif ($diff < 86400) {
            $hours = ceil($diff / 3600);
            return 'Còn ' . $hours . ' giờ';
        } elseif ($diff < 604800) {
            $days = ceil($diff / 86400);
            return 'Còn ' . $days . ' ngày';
        } elseif ($diff < 2592000) {
            $weeks = ceil($diff / 604800);
            return 'Còn ' . $weeks . ' tuần';
        } else {
            return nv_date('d/m/Y', $timestamp);
        }
    } else {
        // Quá hạn
        $diff = abs($diff);
        if ($diff < 3600) {
            $mins = floor($diff / 60);
            return 'Quá hạn ' . $mins . ' phút';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return 'Quá hạn ' . $hours . ' giờ';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return 'Quá hạn ' . $days . ' ngày';
        } else {
            $weeks = floor($diff / 604800);
            return 'Quá hạn ' . $weeks . ' tuần';
        }
    }
}
