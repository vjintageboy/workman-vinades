<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

$lang_translator['author'] = 'VINADES.,JSC <contact@vinades.vn>';
$lang_translator['createdate'] = '04/03/2010, 15:22';
$lang_translator['copyright'] = '@Copyright (C) 2009-2021 VINADES.,JSC. All rights reserved';
$lang_translator['info'] = '';
$lang_translator['langtype'] = 'lang_module';

// ============================================================================
// Menu & Page Titles
// ============================================================================
$lang_module['main'] = 'Trang chính';
$lang_module['add'] = 'Thêm công việc';
$lang_module['edit'] = 'Sửa công việc';
$lang_module['categories'] = 'Danh mục';
$lang_module['reports'] = 'Thống kê & Báo cáo';
$lang_module['settings'] = 'Cài đặt';
$lang_module['my_tasks'] = 'Công việc của tôi';
$lang_module['task_detail'] = 'Chi tiết công việc';

// ============================================================================
// Form Labels
// ============================================================================
$lang_module['title'] = 'Tiêu đề';
$lang_module['description'] = 'Mô tả';
$lang_module['status'] = 'Trạng thái';
$lang_module['priority'] = 'Độ ưu tiên';
$lang_module['due_date'] = 'Ngày hết hạn';
$lang_module['category'] = 'Danh mục';
$lang_module['assigned_to'] = 'Giao cho';
$lang_module['created_by'] = 'Người tạo';
$lang_module['created_at'] = 'Ngày tạo';
$lang_module['updated_at'] = 'Cập nhật lúc';
$lang_module['completed_at'] = 'Hoàn thành lúc';
$lang_module['attachment'] = 'File đính kèm';
$lang_module['attachment_image'] = 'Ảnh đính kèm';
$lang_module['color'] = 'Màu sắc';
$lang_module['weight'] = 'Thứ tự';
$lang_module['select_category'] = '-- Chọn danh mục --';
$lang_module['select_user'] = '-- Chọn người thực hiện --';
$lang_module['select_status'] = '-- Tất cả trạng thái --';

// ============================================================================
// Buttons & Actions
// ============================================================================
$lang_module['save'] = 'Lưu lại';
$lang_module['cancel'] = 'Hủy bỏ';
$lang_module['delete'] = 'Xóa';
$lang_module['back'] = 'Quay lại';
$lang_module['filter'] = 'Lọc';
$lang_module['reset'] = 'Đặt lại';
$lang_module['export'] = 'Xuất báo cáo';
$lang_module['bulk_actions'] = 'Thao tác hàng loạt';
$lang_module['delete_selected'] = 'Xóa đã chọn';
$lang_module['change_status'] = 'Đổi trạng thái';
$lang_module['assign'] = 'Giao việc';
$lang_module['add_comment'] = 'Thêm bình luận';
$lang_module['update_status'] = 'Cập nhật trạng thái';
$lang_module['accept_task'] = 'Nhận việc';
$lang_module['request_review'] = 'Yêu cầu duyệt';
$lang_module['approve'] = 'Duyệt';
$lang_module['reject'] = 'Từ chối';

// ============================================================================
// Status
// ============================================================================
$lang_module['status_draft'] = 'Bản nháp';
$lang_module['status_pending'] = 'Chờ xử lý';
$lang_module['status_doing'] = 'Đang làm';
$lang_module['status_review'] = 'Chờ duyệt';
$lang_module['status_done'] = 'Hoàn thành';
$lang_module['status_cancelled'] = 'Đã hủy';

// Status CSS classes
$lang_module['status_class_draft'] = 'secondary';
$lang_module['status_class_pending'] = 'info';
$lang_module['status_class_doing'] = 'warning';
$lang_module['status_class_review'] = 'primary';
$lang_module['status_class_done'] = 'success';
$lang_module['status_class_cancelled'] = 'danger';

// ============================================================================
// Priority
// ============================================================================
$lang_module['priority_low'] = 'Thấp';
$lang_module['priority_normal'] = 'Bình thường';
$lang_module['priority_high'] = 'Cao';
$lang_module['priority_urgent'] = 'Khẩn cấp';

// Priority CSS classes
$lang_module['priority_class_low'] = 'secondary';
$lang_module['priority_class_normal'] = 'info';
$lang_module['priority_class_high'] = 'warning';
$lang_module['priority_class_urgent'] = 'danger';

// ============================================================================
// Activity Logs
// ============================================================================
$lang_module['log_created'] = 'Tạo mới công việc';
$lang_module['log_updated'] = 'Cập nhật công việc';
$lang_module['log_status_changed'] = 'Thay đổi trạng thái';
$lang_module['log_assigned'] = 'Giao việc';
$lang_module['log_commented'] = 'Thêm bình luận';
$lang_module['log_deleted'] = 'Xóa công việc';
$lang_module['activity_history'] = 'Lịch sử hoạt động';

// ============================================================================
// Notifications
// ============================================================================
$lang_module['notification_assigned'] = 'Bạn được giao công việc mới: %s';
$lang_module['notification_status_changed'] = 'Trạng thái công việc "%s" đã thay đổi thành %s';
$lang_module['notification_commented'] = 'Có bình luận mới trong công việc: %s';
$lang_module['notification_deadline'] = 'Công việc "%s" sắp hết hạn!';
$lang_module['notifications'] = 'Thông báo';
$lang_module['mark_all_read'] = 'Đánh dấu tất cả đã đọc';
$lang_module['no_notifications'] = 'Không có thông báo mới';

// ============================================================================
// Comments
// ============================================================================
$lang_module['comments'] = 'Bình luận';
$lang_module['no_comments'] = 'Chưa có bình luận nào';
$lang_module['comment_placeholder'] = 'Nhập nội dung bình luận...';

// ============================================================================
// Dashboard & Statistics
// ============================================================================
$lang_module['dashboard'] = 'Tổng quan';
$lang_module['total_tasks'] = 'Tổng công việc';
$lang_module['tasks_by_status'] = 'Theo trạng thái';
$lang_module['tasks_by_user'] = 'Theo người thực hiện';
$lang_module['tasks_by_category'] = 'Theo danh mục';
$lang_module['overdue_tasks'] = 'Công việc quá hạn';
$lang_module['upcoming_deadlines'] = 'Sắp hết hạn';
$lang_module['recent_activities'] = 'Hoạt động gần đây';

// ============================================================================
// Categories
// ============================================================================
$lang_module['add_category'] = 'Thêm danh mục';
$lang_module['edit_category'] = 'Sửa danh mục';
$lang_module['category_title'] = 'Tên danh mục';
$lang_module['category_description'] = 'Mô tả danh mục';
$lang_module['category_color'] = 'Màu hiển thị';
$lang_module['no_category'] = 'Không có danh mục';

// ============================================================================
// Errors & Messages
// ============================================================================
$lang_module['error_required_title'] = 'Vui lòng nhập tiêu đề công việc';
$lang_module['error_invalid_status'] = 'Trạng thái không hợp lệ';
$lang_module['error_invalid_transition'] = 'Không được phép chuyển sang trạng thái này';
$lang_module['error_not_found'] = 'Không tìm thấy công việc';
$lang_module['error_permission_denied'] = 'Bạn không có quyền thực hiện thao tác này';
$lang_module['error_category_has_tasks'] = 'Không thể xóa danh mục đang có công việc';
$lang_module['success_saved'] = 'Lưu thành công';
$lang_module['success_deleted'] = 'Xóa thành công';
$lang_module['success_status_updated'] = 'Cập nhật trạng thái thành công';
$lang_module['confirm_delete'] = 'Bạn có chắc muốn xóa?';
$lang_module['confirm_delete_selected'] = 'Bạn có chắc muốn xóa các mục đã chọn?';

// ============================================================================
// Table Headers
// ============================================================================
$lang_module['col_id'] = 'ID';
$lang_module['col_title'] = 'Tiêu đề';
$lang_module['col_status'] = 'Trạng thái';
$lang_module['col_priority'] = 'Ưu tiên';
$lang_module['col_category'] = 'Danh mục';
$lang_module['col_assigned'] = 'Người thực hiện';
$lang_module['col_due_date'] = 'Hạn chót';
$lang_module['col_actions'] = 'Thao tác';

// ============================================================================
// Empty States
// ============================================================================
$lang_module['no_tasks'] = 'Chưa có công việc nào';
$lang_module['no_tasks_assigned'] = 'Bạn chưa được giao công việc nào';
$lang_module['no_results'] = 'Không có kết quả phù hợp';
