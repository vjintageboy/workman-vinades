#!/usr/bin/env php
<?php
/**
 * Script tạo người dùng mới trong NukeViet 5.x
 * 
 * Sử dụng:
 * php scripts/create_user.php --username=johndoe --email=john@example.com --password=MyPass123
 * 
 * Tham số:
 *   --username     Tên đăng nhập (bắt buộc)
 *   --email        Email (bắt buộc)
 *   --password     Mật khẩu (bắt buộc)
 *   --first_name   Họ (tùy chọn)
 *   --last_name    Tên (tùy chọn)
 *   --group_id     ID nhóm mặc định: 4=chính thức, 7=mới (mặc định: 4)
 *   --active       Kích hoạt user: 1=có, 0=không (mặc định: 1)
 */

// Kiểm tra chạy từ CLI
if (php_sapi_name() !== 'cli') {
    die('Script này chỉ chạy được từ command line!');
}

// Bỏ qua lỗi Deprecated và các thông báo không quan trọng để NukeViet không dừng script
error_reporting(0);
ini_set('display_errors', 0);

// Parse arguments
$options = getopt('', [
    'username:',
    'email:',
    'password:',
    'first_name::',
    'last_name::',
    'group_id::',
    'active::'
]);

// Validate required params
if (empty($options['username']) || empty($options['email']) || empty($options['password'])) {
    echo "Sử dụng: php create_user.php --username=... --email=... --password=...\n";
    echo "\nTham số bắt buộc:\n";
    echo "  --username     Tên đăng nhập\n";
    echo "  --email        Email\n";
    echo "  --password     Mật khẩu\n";
    echo "\nTham số tùy chọn:\n";
    echo "  --first_name   Họ\n";
    echo "  --last_name    Tên\n";
    echo "  --group_id     4=chính thức, 7=thành viên mới (mặc định: 4)\n";
    echo "  --active       1=kích hoạt, 0=chưa kích hoạt (mặc định: 1)\n";
    exit(1);
}

// Chuyển đến thư mục gốc NukeViet
chdir(dirname(__DIR__));

// Load NukeViet bootstrap
define('NV_SYSTEM', true);
define('NV_ADMIN', true); // Để có quyền truy cập vào các hàm admin nếu cần
define('NV_ROOTDIR', getcwd() . '/src');

// Giả lập môi trường server cho CLI
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SERVER_NAME'] = 'pha.my';
$_SERVER['HTTP_HOST'] = 'pha.my';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (NukeViet CLI)';

// Tải config
require_once NV_ROOTDIR . '/includes/mainfile.php';

global $db, $db_config, $global_config, $crypt;

// ============================================================================
// Dữ liệu user mới
// ============================================================================
$username = trim($options['username']);
$email = strtolower(trim($options['email']));
$password = $options['password'];
$first_name = $options['first_name'] ?? '';
$last_name = $options['last_name'] ?? '';
$group_id = (int)($options['group_id'] ?? 4);
$active = (int)($options['active'] ?? 1);

// Validate group_id
if (!in_array($group_id, [4, 7])) {
    $group_id = 4;
}

// Tạo md5 username
$md5username = md5(strtolower($username));

// Kiểm tra username đã tồn tại
$stmt = $db->prepare('SELECT userid FROM ' . $db_config['prefix'] . '_users WHERE username = :username OR md5username = :md5username');
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->bindParam(':md5username', $md5username, PDO::PARAM_STR);
$stmt->execute();
if ($stmt->fetchColumn()) {
    echo "LỖI: Username '{$username}' đã tồn tại!\n";
    exit(1);
}

// Kiểm tra email đã tồn tại
$stmt = $db->prepare('SELECT userid FROM ' . $db_config['prefix'] . '_users WHERE email = :email');
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();
if ($stmt->fetchColumn()) {
    echo "LỖI: Email '{$email}' đã tồn tại!\n";
    exit(1);
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "LỖI: Email không hợp lệ!\n";
    exit(1);
}

// Hash password
$password_hash = $crypt->hash_password($password, $global_config['hashprefix']);

// In_groups
$in_groups = $group_id == 4 ? '4' : '7';

// Current time
$currenttime = time();

// ============================================================================
// Insert user
// ============================================================================
try {
    $sql = 'INSERT INTO ' . $db_config['prefix'] . '_users (
        group_id, username, md5username, password, email, 
        first_name, last_name, gender, birthday, sig, 
        regdate, question, answer, passlostkey, view_mail, 
        remember, in_groups, active, checknum, last_login, 
        last_ip, last_agent, last_openid, idsite, 
        pass_creation_time, email_creation_time, email_verification_time
    ) VALUES (
        :group_id, :username, :md5username, :password, :email,
        :first_name, :last_name, "", 0, "",
        :regdate, "", "", "", 0,
        1, :in_groups, :active, "", 0,
        "", "", "", 0,
        :pass_time, :email_time, -1
    )';
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':md5username', $md5username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
    $stmt->bindParam(':regdate', $currenttime, PDO::PARAM_INT);
    $stmt->bindParam(':in_groups', $in_groups, PDO::PARAM_STR);
    $stmt->bindParam(':active', $active, PDO::PARAM_INT);
    $stmt->bindParam(':pass_time', $currenttime, PDO::PARAM_INT);
    $stmt->bindParam(':email_time', $currenttime, PDO::PARAM_INT);
    $stmt->execute();
    
    $userid = $db->lastInsertId();
    
    if ($userid) {
        // Insert vào bảng users_info
        $db->exec('INSERT INTO ' . $db_config['prefix'] . '_users_info (userid) VALUES (' . $userid . ')');
        
        // Insert vào bảng users_groups_users (nếu là thành viên chính thức)
        if ($group_id == 4) {
            $db->exec('INSERT INTO ' . $db_config['prefix'] . '_users_groups_users 
                (group_id, userid, is_leader, approved, data, time_requested, time_approved) 
                VALUES (4, ' . $userid . ', 0, 1, "", ' . $currenttime . ', ' . $currenttime . ')');
        }
        
        // Cập nhật số lượng nhóm
        $db->exec('UPDATE ' . $db_config['prefix'] . '_users_groups SET numbers = numbers + 1 WHERE group_id = ' . $group_id);
        
        echo "✓ Tạo user thành công!\n";
        echo "  - User ID: {$userid}\n";
        echo "  - Username: {$username}\n";
        echo "  - Email: {$email}\n";
        echo "  - Nhóm: " . ($group_id == 4 ? 'Thành viên chính thức' : 'Thành viên mới') . "\n";
        echo "  - Trạng thái: " . ($active ? 'Đã kích hoạt' : 'Chưa kích hoạt') . "\n";
        
    } else {
        echo "LỖI: Không thể tạo user!\n";
        exit(1);
    }
    
} catch (PDOException $e) {
    echo "LỖI Database: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nHoàn tất!\n";
exit(0);
