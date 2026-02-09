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

header('Content-Type: application/json; charset=utf-8');

$response = [
    'error' => 1,
    'message' => $nv_Lang->getGlobal('error_unknown')
];

$action = $nv_Request->get_string('action', 'post', '');
$id = $nv_Request->get_int('id', 'post', 0);

if ($id <= 0) {
    $response['message'] = 'Invalid task ID';
    echo json_encode($response);
    exit;
}

// Kiểm tra task tồn tại
$sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . ' WHERE id = ' . $id . ' AND is_deleted = 0';
$task = $db->query($sql)->fetch();

if (!$task) {
    $response['message'] = 'Task not found';
    echo json_encode($response);
    exit;
}

switch ($action) {
    case 'update_status':
        $new_status = $nv_Request->get_string('status', 'post', '');
        $valid_statuses = ['draft', 'pending', 'doing', 'review', 'done', 'cancelled'];
        
        if (!in_array($new_status, $valid_statuses)) {
            $response['message'] = 'Invalid status';
            break;
        }
        
        $old_status = $task['status'];
        
        // Update status
        $update_data = [
            'status' => $new_status,
            'updated_at' => NV_CURRENTTIME
        ];
        
        // Set completed_at if status changed to done
        if ($new_status == 'done' && $old_status != 'done') {
            $update_data['completed_at'] = NV_CURRENTTIME;
        } elseif ($new_status != 'done' && $old_status == 'done') {
            $update_data['completed_at'] = 0;
        }
        
        $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . ' SET ';
        $updates = [];
        foreach ($update_data as $key => $value) {
            if (is_string($value)) {
                $updates[] = $key . ' = ' . $db->quote($value);
            } else {
                $updates[] = $key . ' = ' . intval($value);
            }
        }
        $sql .= implode(', ', $updates) . ' WHERE id = ' . $id;
        
        try {
            $db->exec($sql);
            
            // Log activity
            $old_text = $nv_Lang->getModule('status_' . $old_status);
            $new_text = $nv_Lang->getModule('status_' . $new_status);
            workman_log_activity($id, 'status_changed', $old_text, $new_text);
            
            $nv_Cache->delMod($module_name);
            
            $response['error'] = 0;
            $response['message'] = $nv_Lang->getModule('status_updated_success');
        } catch (Exception $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
        break;
        
    case 'update_priority':
        $new_priority = $nv_Request->get_string('priority', 'post', '');
        $valid_priorities = ['low', 'normal', 'high', 'urgent'];
        
        if (!in_array($new_priority, $valid_priorities)) {
            $response['message'] = 'Invalid priority';
            break;
        }
        
        $old_priority = $task['priority'];
        
        // Update priority
        $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . ' SET 
                priority = ' . $db->quote($new_priority) . ',
                updated_at = ' . NV_CURRENTTIME . '
                WHERE id = ' . $id;
        
        try {
            $db->exec($sql);
            
            // Log activity
            $old_text = $nv_Lang->getModule('priority_' . $old_priority);
            $new_text = $nv_Lang->getModule('priority_' . $new_priority);
            workman_log_activity($id, 'priority_changed', $old_text, $new_text);
            
            $nv_Cache->delMod($module_name);
            
            $response['error'] = 0;
            $response['message'] = $nv_Lang->getModule('priority_updated_success');
        } catch (Exception $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
        break;
        
    default:
        // Legacy support - if no action specified, treat as status update
        $new_status = $nv_Request->get_string('status', 'post', '');
        if (!empty($new_status)) {
            $valid_statuses = ['draft', 'pending', 'doing', 'review', 'done', 'cancelled'];
            if (in_array($new_status, $valid_statuses)) {
                $old_status = $task['status'];
                
                $update_data = [
                    'status' => $new_status,
                    'updated_at' => NV_CURRENTTIME
                ];
                
                if ($new_status == 'done' && $old_status != 'done') {
                    $update_data['completed_at'] = NV_CURRENTTIME;
                } elseif ($new_status != 'done' && $old_status == 'done') {
                    $update_data['completed_at'] = 0;
                }
                
                $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . ' SET ';
                $updates = [];
                foreach ($update_data as $key => $value) {
                    if (is_string($value)) {
                        $updates[] = $key . ' = ' . $db->quote($value);
                    } else {
                        $updates[] = $key . ' = ' . intval($value);
                    }
                }
                $sql .= implode(', ', $updates) . ' WHERE id = ' . $id;
                
                try {
                    $db->exec($sql);
                    $old_text = $nv_Lang->getModule('status_' . $old_status);
                    $new_text = $nv_Lang->getModule('status_' . $new_status);
                    workman_log_activity($id, 'status_changed', $old_text, $new_text);
                    $nv_Cache->delMod($module_name);
                    $response['error'] = 0;
                    $response['message'] = $nv_Lang->getModule('status_updated_success');
                } catch (Exception $e) {
                    $response['message'] = 'Database error';
                }
            }
        }
        break;
}

echo json_encode($response);
exit;
