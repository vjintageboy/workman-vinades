<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN')) {
    die('Stop!!!');
}

// Menu con (submenu)
$submenu['main'] = $nv_Lang->getModule('main');     // Trang danh sách
$submenu['add'] = $nv_Lang->getModule('add');     // Trang thêm mới

// Khai báo các function được phép hoạt động trong admin
$allow_func[] = 'main';
$allow_func[] = 'add';