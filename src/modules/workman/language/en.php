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
$lang_module['main'] = 'Main page';
$lang_module['add'] = 'Add task';
$lang_module['edit'] = 'Edit task';
$lang_module['categories'] = 'Categories';
$lang_module['reports'] = 'Statistics & Reports';
$lang_module['settings'] = 'Settings';
$lang_module['my_tasks'] = 'My tasks';
$lang_module['task_detail'] = 'Task details';
$lang_module['task_info'] = 'Task information';
$lang_module['activity_log'] = 'Activity log';

// ============================================================================
// Form Labels
// ============================================================================
$lang_module['title'] = 'Title';
$lang_module['description'] = 'Description';
$lang_module['status'] = 'Status';
$lang_module['priority'] = 'Priority';
$lang_module['due_date'] = 'Due date';
$lang_module['category'] = 'Category';
$lang_module['assigned_to'] = 'Assigned to';
$lang_module['created_by'] = 'Created by';
$lang_module['created_at'] = 'Created at';
$lang_module['updated_at'] = 'Updated at';
$lang_module['completed_at'] = 'Completed at';
$lang_module['attachment'] = 'Attachment';
$lang_module['attachment_image'] = 'Image attachment';
$lang_module['color'] = 'Color';
$lang_module['weight'] = 'Order';
$lang_module['select_category'] = '-- Select category --';
$lang_module['select_user'] = '-- Select user --';
$lang_module['select_status'] = '-- All statuses --';

// ============================================================================
// Buttons & Actions
// ============================================================================
$lang_module['save'] = 'Save';
$lang_module['cancel'] = 'Cancel';
$lang_module['delete'] = 'Delete';
$lang_module['back'] = 'Back';
$lang_module['filter'] = 'Filter';
$lang_module['reset'] = 'Reset';
$lang_module['export'] = 'Export report';
$lang_module['bulk_actions'] = 'Bulk actions';
$lang_module['delete_selected'] = 'Delete selected';
$lang_module['change_status'] = 'Change status';
$lang_module['assign'] = 'Assign';
$lang_module['add_comment'] = 'Add comment';
$lang_module['update_status'] = 'Update status';
$lang_module['accept_task'] = 'Accept task';
$lang_module['request_review'] = 'Request review';
$lang_module['approve'] = 'Approve';
$lang_module['reject'] = 'Reject';
$lang_module['confirm_update_status'] = 'Are you sure you want to update the status?';
$lang_module['update_success'] = 'Updated successfully!';

// ============================================================================
// Status
// ============================================================================
$lang_module['status_draft'] = 'Draft';
$lang_module['status_pending'] = 'Pending';
$lang_module['status_doing'] = 'In progress';
$lang_module['status_review'] = 'In review';
$lang_module['status_done'] = 'Done';
$lang_module['status_cancelled'] = 'Cancelled';

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
$lang_module['priority_low'] = 'Low';
$lang_module['priority_normal'] = 'Normal';
$lang_module['priority_high'] = 'High';
$lang_module['priority_urgent'] = 'Urgent';

// Priority CSS classes
$lang_module['priority_class_low'] = 'secondary';
$lang_module['priority_class_normal'] = 'info';
$lang_module['priority_class_high'] = 'warning';
$lang_module['priority_class_urgent'] = 'danger';

// ============================================================================
// Activity Logs
// ============================================================================
$lang_module['log_created'] = 'Created task';
$lang_module['log_updated'] = 'Updated task';
$lang_module['log_status_changed'] = 'Changed status';
$lang_module['log_assigned'] = 'Assigned task';
$lang_module['log_commented'] = 'Added comment';
$lang_module['log_deleted'] = 'Deleted task';
$lang_module['activity_history'] = 'Activity history';

// ============================================================================
// Comments
// ============================================================================
$lang_module['comments'] = 'Comments';
$lang_module['no_comments'] = 'No comments yet';
$lang_module['comment_placeholder'] = 'Enter your comment...';
$lang_module['send_comment'] = 'Send comment';
$lang_module['optional'] = 'optional';

// ============================================================================
// Dashboard & Statistics
// ============================================================================
$lang_module['dashboard'] = 'Dashboard';
$lang_module['total_tasks'] = 'Total tasks';
$lang_module['tasks_by_status'] = 'By status';
$lang_module['tasks_by_user'] = 'By user';
$lang_module['tasks_by_category'] = 'By category';
$lang_module['overdue_tasks'] = 'Overdue tasks';
$lang_module['upcoming_deadlines'] = 'Upcoming deadlines';
$lang_module['recent_activities'] = 'Recent activities';

// ============================================================================
// Categories
// ============================================================================
$lang_module['add_category'] = 'Add category';
$lang_module['edit_category'] = 'Edit category';
$lang_module['category_title'] = 'Category name';
$lang_module['category_description'] = 'Category description';
$lang_module['category_color'] = 'Display color';
$lang_module['no_category'] = 'No category';

// ============================================================================
// Errors & Messages
// ============================================================================
$lang_module['error_required_title'] = 'Please enter the task title';
$lang_module['error_invalid_status'] = 'Invalid status';
$lang_module['error_invalid_transition'] = 'Not allowed to change to this status';
$lang_module['error_not_found'] = 'Task not found';
$lang_module['error_permission_denied'] = 'You do not have permission to perform this action';
$lang_module['error_category_has_tasks'] = 'Cannot delete category with tasks';
$lang_module['success_saved'] = 'Saved successfully';
$lang_module['success_deleted'] = 'Deleted successfully';
$lang_module['success_status_updated'] = 'Status updated successfully';
$lang_module['confirm_delete'] = 'Are you sure you want to delete?';
$lang_module['confirm_delete_selected'] = 'Are you sure you want to delete selected items?';

// ============================================================================
// Table Headers
// ============================================================================
$lang_module['col_id'] = 'ID';
$lang_module['col_title'] = 'Title';
$lang_module['col_status'] = 'Status';
$lang_module['col_priority'] = 'Priority';
$lang_module['col_category'] = 'Category';
$lang_module['col_assigned'] = 'Assignee';
$lang_module['col_due_date'] = 'Due date';
$lang_module['col_actions'] = 'Actions';

// ============================================================================
// Empty States
// ============================================================================
$lang_module['no_tasks'] = 'No tasks yet';
$lang_module['no_tasks_assigned'] = 'You have no assigned tasks';
$lang_module['no_results'] = 'No matching results';

// ============================================================================
// Additional Keys for Admin Templates
// ============================================================================
$lang_module['total'] = 'Total';
$lang_module['stats_by_user'] = 'By user';
$lang_module['stats_by_category'] = 'By category';
$lang_module['assignee'] = 'Assignee';
$lang_module['no_data'] = 'No data yet';
$lang_module['overdue'] = 'Overdue';
$lang_module['no_overdue_tasks'] = 'No overdue tasks';
$lang_module['days'] = 'days';
$lang_module['no_activity'] = 'No activity yet';
$lang_module['no_description'] = 'No description';
$lang_module['attachment_optional'] = 'Attach file (optional)';
$lang_module['quick_status_change'] = 'Quick status change';
$lang_module['category_name'] = 'Category name';
$lang_module['display_color'] = 'Display color';
$lang_module['weight_short'] = 'Order';
$lang_module['task_count'] = 'Task count';
$lang_module['actions'] = 'Actions';
$lang_module['no_category_yet'] = 'No categories yet. Please add a new category.';
$lang_module['confirm_delete_category'] = 'Are you sure you want to delete this category?';
$lang_module['active'] = 'Active';
$lang_module['inactive'] = 'Inactive';

