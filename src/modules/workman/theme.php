<?php

/**
 * NukeViet Content Management System - Workman Module
 * Theme configuration file
 * @version 5.x
 */

if (!defined('NV_SYSTEM')) {
    exit('Stop!!!');
}

// Define module constant
define('NV_IS_MOD_WORKMAN', true);

// Các function cho phép ở frontend
$funcs = [
    'main',      // Dashboard
    'list',      // Danh sách công việc
    'detail',    // Chi tiết công việc
    'update',    // Cập nhật status (AJAX)
    'comment'    // Thêm comment
];
