<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    die('Stop!!!');
}

// $allow_func = [
//     'main',
//     'add',
//     'detail',
//     'categories',
//     'reports'
// ];

define('NV_IS_FILE_ADMIN', true);

// Load CSS for workman module
global $my_head, $global_config, $module_info;
$my_head .= '<link rel="stylesheet" href="' . NV_STATIC_URL . 'themes/' . $global_config['admin_theme'] . '/css/workman.css?t=' . $global_config['timestamp'] . '">' . "\n";

// ============================================================================
// HELPER FUNCTIONS FOR WORKMAN MODULE
// ============================================================================

/**
 * Lấy danh sách categories đang active
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
        // ignore
    }
    
    return $categories;
}

/**
 * Lấy danh sách users có thể assign công việc
 */
function workman_get_users()
{
    global $db, $db_config;
    
    $users = [];
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
        // ignore
    }
    
    return $users;
}

/**
 * Lấy thông tin user theo ID
 */
function workman_get_user_info($user_id)
{
    global $db, $db_config;
    
    $user_id = intval($user_id);
    if ($user_id <= 0) return false;
    
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
        // ignore
    }
    
    return false;
}

/**
 * Ghi log hoạt động
 */
function workman_log_activity($work_id, $action, $old_value = '', $new_value = '')
{
    global $db, $db_config, $module_data, $admin_info, $user_info;
    
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
        return false;
    }
}

/**
 * Tạo thông báo cho user
 */
function workman_notify($user_id, $work_id, $type, $message)
{
    global $db, $db_config, $module_data;
    
    if ($user_id <= 0) return false;
    
    $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_notifications 
            (user_id, work_id, type, message, is_read, created_at) VALUES 
            (' . intval($user_id) . ', ' . intval($work_id) . ', ' . $db->quote($type) . ', 
             ' . $db->quote($message) . ', 0, ' . NV_CURRENTTIME . ')';
    
    try {
        $db->exec($sql);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Lấy danh sách status với label
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
 * Validate chuyển đổi trạng thái
 */
function workman_validate_status_transition($old_status, $new_status, $is_admin = false)
{
    $admin_transitions = [
        'draft' => ['pending', 'cancelled'],
        'pending' => ['doing', 'cancelled'],
        'doing' => ['review', 'cancelled'],
        'review' => ['done', 'doing'],
        'done' => [],
        'cancelled' => ['draft']
    ];
    
    $user_transitions = [
        'pending' => ['doing'],
        'doing' => ['review'],
        'review' => [],
        'done' => [],
        'cancelled' => []
    ];
    
    $allowed = $is_admin ? $admin_transitions : $user_transitions;
    
    if (!isset($allowed[$old_status])) return false;
    
    return in_array($new_status, $allowed[$old_status]);
}

/**
 * Đếm số task theo status
 */
function workman_count_tasks_by_status($user_id = 0)
{
    global $db, $db_config, $module_data;
    
    $counts = [
        'draft' => 0, 'pending' => 0, 'doing' => 0, 
        'review' => 0, 'done' => 0, 'cancelled' => 0, 'total' => 0
    ];
    
    $where = 'is_deleted = 0';
    if ($user_id > 0) $where .= ' AND assigned_to = ' . intval($user_id);
    
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
        // ignore
    }
    
    return $counts;
}