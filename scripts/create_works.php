#!/usr/bin/env php
<?php
/**
 * Script tạo hàng loạt công việc trong module Workman
 * 
 * Sử dụng:
 * php scripts/create_works.php --count=100
 * php scripts/create_works.php --count=50 --status=doing --priority=high
 * php scripts/create_works.php --json=works.json
 * 
 * Tham số:
 *   --count       Số lượng công việc cần tạo (mặc định: 10)
 *   --status      Trạng thái: draft, pending, doing, review, done (mặc định: random)
 *   --priority    Độ ưu tiên: low, normal, high, urgent (mặc định: random)
 *   --category    ID danh mục (mặc định: 1)
 *   --user_id     ID người tạo (mặc định: 1)
 *   --assigned    ID người được giao (mặc định: random từ user có sẵn)
 *   --json        File JSON chứa danh sách công việc cụ thể
 *   --prefix      Tiền tố tiêu đề (mặc định: "Công việc")
 */

// Kiểm tra chạy từ CLI
if (php_sapi_name() !== 'cli') {
    die('Script này chỉ chạy được từ command line!');
}

// Bỏ qua lỗi Deprecated
error_reporting(0);
ini_set('display_errors', 0);

// Parse arguments
$options = getopt('', [
    'count::',
    'status::',
    'priority::',
    'category::',
    'user_id::',
    'assigned::',
    'json::',
    'prefix::',
    'help'
]);

// Help
if (isset($options['help'])) {
    echo "Sử dụng: php create_works.php [options]\n\n";
    echo "Tham số:\n";
    echo "  --count       Số lượng công việc (mặc định: 10)\n";
    echo "  --status      Trạng thái: draft, pending, doing, review, done, cancelled\n";
    echo "  --priority    Độ ưu tiên: low, normal, high, urgent\n";
    echo "  --category    ID danh mục (mặc định: 1)\n";
    echo "  --user_id     ID người tạo (mặc định: 1)\n";
    echo "  --assigned    ID người được giao\n";
    echo "  --json        File JSON chứa danh sách công việc\n";
    echo "  --prefix      Tiền tố tiêu đề (mặc định: 'Công việc')\n";
    echo "  --help        Hiện trợ giúp\n\n";
    echo "Ví dụ:\n";
    echo "  php create_works.php --count=100\n";
    echo "  php create_works.php --count=50 --status=pending --priority=high\n";
    echo "  php create_works.php --json=my_works.json\n";
    exit(0);
}

// Chuyển đến thư mục gốc NukeViet
chdir(dirname(__DIR__));

// Load NukeViet bootstrap
define('NV_SYSTEM', true);
define('NV_ADMIN', true);
define('NV_ROOTDIR', getcwd() . '/src');

// Giả lập môi trường server cho CLI
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SERVER_NAME'] = 'pha.my';
$_SERVER['HTTP_HOST'] = 'pha.my';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (NukeViet CLI)';

// Tải config
require_once NV_ROOTDIR . '/includes/mainfile.php';

global $db, $db_config;

// ============================================================================
// Cấu hình
// ============================================================================
$module_data = 'workman';
$table = $db_config['prefix'] . '_' . $module_data;

$statuses = ['draft', 'pending', 'doing', 'review', 'done', 'cancelled'];
$priorities = ['low', 'normal', 'high', 'urgent'];

// Sample titles cho random
$sample_titles = [
    'Thiết kế giao diện trang chủ',
    'Phát triển API backend',
    'Kiểm thử chức năng đăng nhập',
    'Tối ưu hiệu suất database',
    'Viết tài liệu hướng dẫn',
    'Cập nhật bảo mật hệ thống',
    'Tích hợp thanh toán online',
    'Xây dựng hệ thống thông báo',
    'Refactor code module user',
    'Deploy lên production server',
    'Fix bug trang danh sách',
    'Thêm chức năng tìm kiếm',
    'Cải thiện UX form đăng ký',
    'Backup dữ liệu định kỳ',
    'Review code pull request',
    'Họp planning sprint mới',
    'Nghiên cứu công nghệ mới',
    'Viết unit test cho module',
    'Tạo báo cáo thống kê',
    'Cấu hình CI/CD pipeline'
];

$sample_descriptions = [
    'Cần hoàn thành trước deadline đã định.',
    'Ưu tiên cao, cần phối hợp với team design.',
    'Đã có tài liệu mô tả chi tiết trong Confluence.',
    'Liên hệ với PM nếu có thắc mắc.',
    'Kiểm tra kỹ trên các môi trường khác nhau.',
    'Tham khảo code mẫu trong repository.',
    'Cập nhật tiến độ hàng ngày.',
    'Yêu cầu review trước khi merge.',
];

// ============================================================================
// Parse options
// ============================================================================
$count = (int)($options['count'] ?? 10);
$fixed_status = $options['status'] ?? null;
$fixed_priority = $options['priority'] ?? null;
$category_id = (int)($options['category'] ?? 1);
$user_id = (int)($options['user_id'] ?? 1);
$assigned_to = isset($options['assigned']) ? (int)$options['assigned'] : null;
$json_file = $options['json'] ?? null;
$prefix = $options['prefix'] ?? 'Công việc';

// Validate status
if ($fixed_status && !in_array($fixed_status, $statuses)) {
    echo "LỖI: Status không hợp lệ! Chọn: " . implode(', ', $statuses) . "\n";
    exit(1);
}

// Validate priority
if ($fixed_priority && !in_array($fixed_priority, $priorities)) {
    echo "LỖI: Priority không hợp lệ! Chọn: " . implode(', ', $priorities) . "\n";
    exit(1);
}

echo "========================================\n";
echo "   WORKMAN - Tạo hàng loạt công việc   \n";
echo "========================================\n\n";

// ============================================================================
// Lấy danh sách user_id có sẵn để random assign
// ============================================================================
$available_users = [];
$stmt = $db->query('SELECT userid FROM ' . $db_config['prefix'] . '_users WHERE active = 1 LIMIT 100');
while ($row = $stmt->fetch()) {
    $available_users[] = (int)$row['userid'];
}
if (empty($available_users)) {
    $available_users = [1];
}

echo "→ Tìm thấy " . count($available_users) . " user có sẵn\n";

// ============================================================================
// Chuẩn bị dữ liệu công việc
// ============================================================================
$works = [];

if ($json_file) {
    // Đọc từ file JSON
    if (!file_exists($json_file)) {
        echo "LỖI: Không tìm thấy file {$json_file}!\n";
        exit(1);
    }
    $json_data = file_get_contents($json_file);
    $works = json_decode($json_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "LỖI: File JSON không hợp lệ!\n";
        exit(1);
    }
    $count = count($works);
    echo "→ Đọc {$count} công việc từ file JSON\n";
} else {
    // Tạo ngẫu nhiên
    echo "→ Chuẩn bị tạo {$count} công việc...\n";
    
    for ($i = 1; $i <= $count; $i++) {
        $random_title = $sample_titles[array_rand($sample_titles)];
        $random_desc = $sample_descriptions[array_rand($sample_descriptions)];
        
        // Random due_date trong vòng 30 ngày tới
        $due_date = time() + rand(86400, 86400 * 30);
        
        $works[] = [
            'title' => "{$prefix} #{$i}: {$random_title}",
            'description' => $random_desc,
            'status' => $fixed_status ?? $statuses[array_rand($statuses)],
            'priority' => $fixed_priority ?? $priorities[array_rand($priorities)],
            'due_date' => $due_date,
            'category_id' => $category_id,
            'created_by' => $user_id,
            'assigned_to' => $assigned_to ?? $available_users[array_rand($available_users)]
        ];
    }
}

// ============================================================================
// Insert vào database bằng Batch INSERT
// ============================================================================
echo "\n→ Bắt đầu insert vào database...\n";

$start_time = microtime(true);
$now = time();
$success_count = 0;
$error_count = 0;

try {
    $db->beginTransaction();
    
    // Chia thành chunks để tránh query quá dài
    $chunks = array_chunk($works, 100);
    
    foreach ($chunks as $chunk_index => $chunk) {
        $values = [];
        
        foreach ($chunk as $work) {
            $title = $db->quote($work['title']);
            $description = $db->quote($work['description'] ?? '');
            $status = $db->quote($work['status'] ?? 'draft');
            $priority = $db->quote($work['priority'] ?? 'normal');
            $due_date = isset($work['due_date']) ? (int)$work['due_date'] : 0;
            $created_by = (int)($work['created_by'] ?? $user_id);
            $assigned_to = (int)($work['assigned_to'] ?? 0);
            $cat_id = (int)($work['category_id'] ?? $category_id);
            $completed_at = $work['status'] === 'done' ? $now : 'NULL';
            
            $values[] = "({$title}, {$description}, {$status}, {$priority}, {$due_date}, '', {$created_by}, {$assigned_to}, {$cat_id}, {$now}, {$now}, {$completed_at}, 0, NULL)";
        }
        
        if (!empty($values)) {
            $sql = "INSERT INTO {$table} 
                (title, description, status, priority, due_date, attachment, 
                 created_by, assigned_to, category_id, created_at, updated_at, 
                 completed_at, is_deleted, deleted_at) 
                VALUES " . implode(', ', $values);
            
            $result = $db->exec($sql);
            $success_count += count($chunk);
            
            echo "  ✓ Chunk " . ($chunk_index + 1) . "/" . count($chunks) . ": Đã insert " . count($chunk) . " records\n";
        }
    }
    
    $db->commit();
    
} catch (PDOException $e) {
    $db->rollBack();
    echo "LỖI Database: " . $e->getMessage() . "\n";
    exit(1);
}

$end_time = microtime(true);
$elapsed = round(($end_time - $start_time) * 1000, 2);

// ============================================================================
// Thống kê kết quả
// ============================================================================
echo "\n========================================\n";
echo "   KẾT QUẢ\n";
echo "========================================\n";
echo "✓ Tạo thành công: {$success_count} công việc\n";
echo "✗ Lỗi: {$error_count} công việc\n";
echo "⏱ Thời gian: {$elapsed}ms\n";

// Đếm theo status
$stmt = $db->query("SELECT status, COUNT(*) as cnt FROM {$table} WHERE is_deleted = 0 GROUP BY status");
echo "\nPhân bố theo trạng thái:\n";
while ($row = $stmt->fetch()) {
    echo "  - {$row['status']}: {$row['cnt']}\n";
}

// Tổng số
$total = $db->query("SELECT COUNT(*) FROM {$table} WHERE is_deleted = 0")->fetchColumn();
echo "\nTổng số công việc trong hệ thống: {$total}\n";

echo "\nHoàn tất!\n";
exit(0);
