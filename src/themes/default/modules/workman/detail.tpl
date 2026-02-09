<!-- BEGIN: main -->
<div class="workman-dashboard workman-detail-page">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <h3><i class="fa fa-file-text"></i> Chi tiết công việc</h3>
            <p class="text-muted">Công việc #{TASK_ID}: {TASK.title}</p>
        </div>
        <a href="{URL_LIST}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
    
    <!-- Main Content Grid -->
    <div class="row">
        <!-- Left Column: Task Info -->
        <div class="col-xs-24 col-md-16">
            <!-- Task Header Card -->
            <div class="task-panel task-panel-primary">
                <div class="task-panel-header">
                    <div class="panel-title-icon"><i class="fa fa-info-circle"></i></div>
                    <h4>Thông tin công việc</h4>
                    <span class="task-status status-{TASK.status}">{TASK.status_text}</span>
                </div>
                <div class="task-panel-body detail-body">
                    <div class="detail-grid">
                        <div class="detail-row">
                            <div class="detail-label">
                                <i class="fa fa-flag"></i> Độ ưu tiên
                            </div>
                            <div class="detail-value">
                                <span class="priority-badge priority-{TASK.priority}">{TASK.priority_text}</span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">
                                <i class="fa fa-folder"></i> Danh mục
                            </div>
                            <div class="detail-value">
                                <span class="task-category" style="background-color: {TASK.category_color};">{TASK.category_title}</span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">
                                <i class="fa fa-user"></i> Người giao
                            </div>
                            <div class="detail-value">
                                <span class="creator-name">{TASK.creator_name}</span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">
                                <i class="fa fa-calendar"></i> Hạn chốt
                            </div>
                            <div class="detail-value">
                                <span class="{TASK.due_date_class}">{TASK.due_date_display}</span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">
                                <i class="fa fa-clock-o"></i> Tạo lúc
                            </div>
                            <div class="detail-value">
                                <span class="created-date">{TASK.created_at_formatted}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="task-panel task-panel-default">
                <div class="task-panel-header">
                    <div class="panel-title-icon"><i class="fa fa-file-text-o"></i></div>
                    <h4>Mô tả chi tiết</h4>
                </div>
                <div class="task-panel-body detail-body">
                    <div class="description-content">
                        {TASK.description|noescape}
                    </div>
                </div>
            </div>
            
            <!-- BEGIN: attachment_image -->
            <div class="task-panel task-panel-info">
                <div class="task-panel-header">
                    <div class="panel-title-icon"><i class="fa fa-image"></i></div>
                    <h4>Hình ảnh đính kèm</h4>
                </div>
                <div class="task-panel-body detail-body">
                    <div class="attachment-preview">
                        <a href="{TASK.attachment_url}" target="_blank" class="attachment-image-link">
                            <img src="{TASK.attachment_url}" alt="{TASK.attachment_name}" class="img-responsive">
                            <div class="attachment-overlay">
                                <i class="fa fa-search-plus"></i>
                                <span>Xem lớn</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <!-- END: attachment_image -->
            
            <!-- BEGIN: attachment_file -->
            <div class="task-panel task-panel-info">
                <div class="task-panel-header">
                    <div class="panel-title-icon"><i class="fa fa-paperclip"></i></div>
                    <h4>File đính kèm</h4>
                </div>
                <div class="task-panel-body detail-body">
                    <a href="{TASK.attachment_url}" target="_blank" class="attachment-file-link">
                        <div class="file-icon">
                            <i class="fa fa-file"></i>
                        </div>
                        <div class="file-info">
                            <span class="file-name">{TASK.attachment_name}</span>
                            <span class="file-action"><i class="fa fa-download"></i> Tải xuống</span>
                        </div>
                    </a>
                </div>
            </div>
            <!-- END: attachment_file -->
            
            <!-- Submission Section - Nộp kết quả công việc -->
            <div class="task-panel task-panel-success">
                <div class="task-panel-header">
                    <div class="panel-title-icon"><i class="fa fa-upload"></i></div>
                    <h4>Kết quả công việc</h4>
                    <span class="file-count-badge">{SUBMISSION_FILE_COUNT} file</span>
                </div>
                <div class="task-panel-body">
                    <!-- Danh sách submissions đã nộp -->
                    <div class="submissions-list">
                        <!-- BEGIN: submission -->
                        <div class="submission-item-compact" id="submission-{SUBMISSION.id}" data-submission-id="{SUBMISSION.id}">
                            <div class="submission-header-compact">
                                <div class="submission-meta">
                                    <span class="submission-number">Lần #{SUBMISSION.number}</span>
                                    <i class="fa fa-user-circle"></i> <strong>{SUBMISSION.user_fullname}</strong>
                                    <span class="text-muted small">&bull; {SUBMISSION.created_at_formatted}</span>
                                </div>
                                <div class="submission-files-inline">
                                    <!-- BEGIN: sub_file -->
                                    <a href="{SUB_FILE.url}" target="_blank" class="file-link-inline" title="{SUB_FILE.filename} ({SUB_FILE.filesize_formatted})">
                                        <i class="fa fa-paperclip"></i> {SUB_FILE.filename}
                                    </a>
                                    <!-- END: sub_file -->
                                </div>
                                <div class="submission-actions-compact">
                                    <button class="btn-toggle-desc" onclick="toggleSubmissionDesc({SUBMISSION.id})" title="Xem mô tả">
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <!-- BEGIN: delete_action -->
                                    <button class="btn-delete-submission" onclick="deleteSubmission({SUBMISSION.id})" title="Xóa lần nộp này">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <!-- END: delete_action -->
                                </div>
                            </div>
                            <div class="submission-desc-expandable" id="desc-{SUBMISSION.id}" style="display:none;">
                                <div class="submission-desc-content">
                                    {SUBMISSION.description|noescape}
                                </div>
                            </div>
                        </div>
                        <!-- END: submission -->
                        
                        <!-- BEGIN: no_submissions -->
                        <div class="empty-state small">
                            <i class="fa fa-cloud-upload"></i>
                            <p>Chưa có kết quả nào được nộp</p>
                        </div>
                        <!-- END: no_submissions -->
                    </div>
                    
                    <!-- Form nộp kết quả - Collapsible -->
                    <!-- BEGIN: submission_form -->
                    <div class="submission-form-toggle">
                        <button type="button" class="btn btn-success btn-sm" onclick="toggleSubmissionForm()">
                            <i class="fa fa-plus-circle"></i> Nộp kết quả mới
                        </button>
                    </div>
                    
                    <div class="submission-form-wrapper" id="submissionFormWrapper" style="display:none;">
                        <form id="submissionForm" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="work_id" value="{TASK_ID}">
                            <div class="form-group">
                                <label>Mô tả kết quả <span class="text-danger">*</span></label>
                                <textarea name="description" id="submissionDescription" class="form-control" rows="3" 
                                    placeholder="Mô tả chi tiết công việc đã hoàn thành..." required minlength="10"></textarea>
                                <small class="help-block">Tối thiểu 10 ký tự</small>
                            </div>
                            <div class="form-group">
                                <label>File kết quả <span class="text-danger">*</span></label>
                                <div class="file-upload-zone" id="fileUploadZone">
                                    <i class="fa fa-cloud-upload"></i>
                                    <p>Kéo thả file vào đây hoặc <label for="submissionFiles">chọn file</label></p>
                                    <small>Tối đa 5 file, mỗi file không quá 10MB</small>
                                    <input type="file" name="files[]" id="submissionFiles" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
                                </div>
                                <div id="filePreviewList" class="file-preview-list"></div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-upload"></i> Nộp kết quả
                                </button>
                                <button type="button" class="btn btn-default" onclick="toggleSubmissionForm()">
                                    <i class="fa fa-times"></i> Hủy
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- END: submission_form -->
                </div>
            </div>
            
            <!-- Comments Section - Gộp Bình luận + Form -->
            <div class="task-panel task-panel-warning">
                <div class="task-panel-header">
                    <div class="panel-title-icon"><i class="fa fa-comments"></i></div>
                    <h4>Bình luận & Thảo luận</h4>
                </div>
                <div class="task-panel-body">
                    <!-- Comments List -->
                    <div class="comments-list">
                        <!-- BEGIN: comment -->
                        <div class="comment-item">
                            <div class="comment-avatar">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="comment-content">
                                <div class="comment-header">
                                    <strong class="comment-author">{COMMENT.user_fullname}</strong>
                                    <!-- BEGIN: is_admin -->
                                    <span class="badge badge-admin">Admin</span>
                                    <!-- END: is_admin -->
                                    <span class="comment-time">
                                        <i class="fa fa-clock-o"></i> {COMMENT.created_at_formatted}
                                    </span>
                                </div>
                                <div class="comment-text">{COMMENT.content|noescape}</div>
                                <!-- BEGIN: attachment -->
                                <div class="comment-attachment">
                                    <a href="{COMMENT.attachment_url}" target="_blank">
                                        <i class="fa fa-paperclip"></i> {COMMENT.attachment_name}
                                    </a>
                                </div>
                                <!-- END: attachment -->
                            </div>
                        </div>
                        <!-- END: comment -->
                        
                        <!-- BEGIN: no_comments -->
                        <div class="empty-state small">
                            <i class="fa fa-comments-o"></i>
                            <p>Chưa có bình luận nào. Hãy là người đầu tiên!</p>
                        </div>
                        <!-- END: no_comments -->
                    </div>
                    
                    <!-- Comment Form - Inline -->
                    <!-- BEGIN: comment_form -->
                    <div class="comment-form-inline">
                        <form method="post" action="{URL_COMMENT}" enctype="multipart/form-data">
                            <input type="hidden" name="work_id" value="{TASK_ID}">
                            <div class="comment-input-wrapper">
                                <textarea name="content" id="commentContent" class="form-control" rows="3" placeholder="Nhập nội dung bình luận..." required></textarea>
                            </div>
                            <div class="comment-form-actions">
                                <label class="file-attach-btn">
                                    <i class="fa fa-paperclip"></i>
                                    <span>Đính kèm</span>
                                    <input type="file" name="attachment" class="file-input-hidden">
                                </label>
                                <button type="submit" name="submit_comment" class="btn btn-warning">
                                    <i class="fa fa-send"></i> Gửi
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- END: comment_form -->
                </div>
            </div>
        </div>
        
        <!-- Right Column: Sidebar -->
        <div class="col-xs-24 col-md-8">
            <div class="detail-sidebar">
                <!-- Quick Actions -->
                <!-- BEGIN: action_accept -->
                <div class="sidebar-action-card action-accept">
                    <div class="action-icon">
                        <i class="fa fa-hand-paper-o"></i>
                    </div>
                    <div class="action-info">
                        <h5>Nhận công việc này</h5>
                        <p>Bắt đầu thực hiện ngay</p>
                    </div>
                    <button type="button" class="btn btn-success btn-block" onclick="updateStatus('doing');">
                        <i class="fa fa-check"></i> Nhận việc
                    </button>
                </div>
                <!-- END: action_accept -->
                
                <!-- BEGIN: action_review -->
                <div class="sidebar-action-card action-review">
                    <div class="action-icon">
                        <i class="fa fa-paper-plane"></i>
                    </div>
                    <div class="action-info">
                        <h5>Gửi duyệt</h5>
                        <p>Đã hoàn thành? Gửi cho quản lý kiểm tra</p>
                    </div>
                    <button type="button" class="btn btn-primary btn-block" onclick="updateStatus('review');">
                        <i class="fa fa-paper-plane"></i> Gửi duyệt
                    </button>
                </div>
                <!-- END: action_review -->

                
                <!-- Activity Log -->
                <div class="task-panel task-panel-default sidebar-panel activity-panel">
                    <div class="task-panel-header">
                        <div class="panel-title-icon"><i class="fa fa-history"></i></div>
                        <h4>Lịch sử hoạt động</h4>
                    </div>
                    <div class="task-panel-body">
                        <!-- BEGIN: log -->
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div class="activity-content">
                                <div>
                                    <span class="activity-user">{LOG.user_fullname}</span>
                                    <span class="activity-action">{LOG.action_text}</span>
                                </div>
                                <span class="activity-time">
                                    <i class="fa fa-clock-o"></i> {LOG.created_at_formatted}
                                </span>
                            </div>
                        </div>
                        <!-- END: log -->
                        
                        <!-- BEGIN: no_logs -->
                        <div class="empty-state small">
                            <i class="fa fa-history"></i>
                            <p>Chưa có hoạt động</p>
                        </div>
                        <!-- END: no_logs -->
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="quick-links-card">
                    <a href="{URL_LIST}" class="quick-link">
                        <i class="fa fa-list"></i>
                        <span>Xem tất cả công việc</span>
                    </a>
                    <a href="{URL_LIST}&status=doing" class="quick-link">
                        <i class="fa fa-tasks"></i>
                        <span>Việc đang làm</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <style>
/* Detail Page Specific Styles */
.workman-detail-page .detail-body {
    padding: 20px;
}

.workman-detail-page .detail-grid {
    display: flex;
    flex-direction: column;
}

.workman-detail-page .detail-row {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.workman-detail-page .detail-row:last-child {
    border-bottom: none;
}

.workman-detail-page .detail-label {
    width: 130px;
    font-weight: 600;
    color: #666;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.workman-detail-page .detail-label i {
    color: #428bca;
    width: 16px;
    text-align: center;
}

.workman-detail-page .detail-value {
    flex: 1;
}

.workman-detail-page .priority-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.workman-detail-page .priority-low { background: #e8f8f0; color: #27ae60; }
.workman-detail-page .priority-normal { background: #e8f4fd; color: #3498db; }
.workman-detail-page .priority-high { background: #fef5e7; color: #e67e22; }
.workman-detail-page .priority-urgent { background: #fbe9e7; color: #e74c3c; }

.workman-detail-page .task-status {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.workman-detail-page .task-status.status-pending { background: #e8f4fd; color: #3498db; }
.workman-detail-page .task-status.status-doing { background: #fef5e7; color: #e67e22; }
.workman-detail-page .task-status.status-review { background: #f4e6f8; color: #9b59b6; }
.workman-detail-page .task-status.status-done { background: #e8f8f0; color: #27ae60; }
.workman-detail-page .task-status.status-cancelled { background: #fbe9e7; color: #e74c3c; }

.workman-detail-page .due-date.overdue {
    color: #e74c3c;
    font-weight: 600;
}

/* Description Content */
.workman-detail-page .description-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    white-space: pre-wrap;
    word-wrap: break-word;
    min-height: 60px;
    line-height: 1.6;
    color: #555;
    border: 1px solid #eee;
}

/* Attachment Preview */
.workman-detail-page .attachment-preview {
    text-align: center;
}

.workman-detail-page .attachment-image-link {
    display: inline-block;
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.workman-detail-page .attachment-image-link img {
    max-height: 350px;
    border-radius: 8px;
}

.workman-detail-page .attachment-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: #fff;
}

.workman-detail-page .attachment-image-link:hover .attachment-overlay {
    opacity: 1;
}

.workman-detail-page .attachment-overlay i {
    font-size: 28px;
    margin-bottom: 8px;
}

/* File Attachment */
.workman-detail-page .attachment-file-link {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1px solid #eee;
}

.workman-detail-page .attachment-file-link:hover {
    background: #e9ecef;
    border-color: #428bca;
}

.workman-detail-page .file-icon {
    width: 45px;
    height: 45px;
    border-radius: 8px;
    background: linear-gradient(135deg, #428bca, #2a6496);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 20px;
    margin-right: 12px;
}

.workman-detail-page .file-name {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 2px;
}

.workman-detail-page .file-action {
    font-size: 12px;
    color: #428bca;
}

/* =================================================================
   SIDEBAR STYLES
   ================================================================= */
.workman-detail-page .detail-sidebar {
    position: sticky;
    top: 20px;
}

/* Sidebar Action Cards */
.workman-detail-page .sidebar-action-card {
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 15px;
    text-align: center;
    box-shadow: 0 3px 12px rgba(0,0,0,0.08);
}

.workman-detail-page .sidebar-action-card.action-accept {
    background: linear-gradient(135deg, rgba(92,184,92,0.1) 0%, rgba(68,157,68,0.15) 100%);
    border: 1px solid rgba(92,184,92,0.3);
}

.workman-detail-page .sidebar-action-card.action-review {
    background: linear-gradient(135deg, rgba(66,139,202,0.1) 0%, rgba(42,100,150,0.15) 100%);
    border: 1px solid rgba(66,139,202,0.3);
}

.workman-detail-page .sidebar-action-card .action-icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.workman-detail-page .action-accept .action-icon { color: #5cb85c; }
.workman-detail-page .action-review .action-icon { color: #428bca; }

.workman-detail-page .sidebar-action-card h5 {
    margin: 0 0 5px 0;
    font-weight: 600;
    color: #333;
}

.workman-detail-page .sidebar-action-card p {
    margin: 0 0 15px 0;
    font-size: 12px;
    color: #777;
}

.workman-detail-page .sidebar-action-card .btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
}

/* Sidebar Panels */
.workman-detail-page .sidebar-panel {
    min-height: auto;
}

.workman-detail-page .sidebar-panel .task-panel-header {
    padding: 12px 15px;
}

.workman-detail-page .sidebar-panel .task-panel-header h4 {
    font-size: 14px;
}

.workman-detail-page .sidebar-panel .panel-title-icon {
    width: 32px;
    height: 32px;
    font-size: 14px;
}

/* Stat Items */
.workman-detail-page .stat-item {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    border-bottom: 1px solid #f0f0f0;
}

.workman-detail-page .stat-item:last-child {
    border-bottom: none;
}

.workman-detail-page .stat-icon-small {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: #e8f4fd;
    color: #428bca;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    font-size: 14px;
}

.workman-detail-page .stat-info {
    flex: 1;
}

.workman-detail-page .stat-label {
    display: block;
    font-size: 11px;
    color: #999;
    text-transform: uppercase;
}

.workman-detail-page .stat-value {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #333;
}

/* Activity Items in Sidebar */
.workman-detail-page .activity-item {
    display: flex;
    align-items: flex-start;
    padding: 10px 15px;
    border-bottom: 1px solid #f0f0f0;
}

.workman-detail-page .activity-item:last-child {
    border-bottom: none;
}

.workman-detail-page .activity-dot {
    width: 8px;
    height: 8px;
    background: #5cb85c;
    border-radius: 50%;
    margin-right: 10px;
    margin-top: 5px;
    flex-shrink: 0;
}

.workman-detail-page .activity-content {
    flex: 1;
    font-size: 12px;
    line-height: 1.4;
}

.workman-detail-page .activity-user {
    font-weight: 600;
    color: #333;
}

.workman-detail-page .activity-action {
    color: #666;
}

.workman-detail-page .activity-time {
    display: block;
    font-size: 10px;
    color: #999;
    margin-top: 3px;
}

/* Quick Links */
.workman-detail-page .quick-links-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 10px;
    margin-top: 15px;
}

.workman-detail-page .quick-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 6px;
    color: #555;
    text-decoration: none;
    font-size: 13px;
    transition: all 0.2s;
}

.workman-detail-page .quick-link:hover {
    background: #fff;
    color: #428bca;
}

.workman-detail-page .quick-link i {
    width: 20px;
    text-align: center;
}

/* =================================================================
   COMMENTS SECTION - Combined
   ================================================================= */
.workman-detail-page .comments-list {
    max-height: 400px;
    overflow-y: auto;
}

.workman-detail-page .comment-item {
    display: flex;
    padding: 12px 15px;
    border-bottom: 1px solid #f0f0f0;
}

.workman-detail-page .comment-item:hover {
    background-color: #fdfbf5;
}

.workman-detail-page .comment-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f0ad4e, #eb9316);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    margin-right: 10px;
    flex-shrink: 0;
    font-size: 16px;
}

.workman-detail-page .comment-content {
    flex: 1;
}

.workman-detail-page .comment-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.workman-detail-page .comment-author {
    color: #333;
    font-size: 13px;
}

.workman-detail-page .badge-admin {
    display: inline-block;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.workman-detail-page .comment-time {
    font-size: 11px;
    color: #999;
    margin-left: auto;
}

.workman-detail-page .comment-text {
    font-size: 13px;
    line-height: 1.5;
    color: #555;
}

.workman-detail-page .comment-attachment a {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    background: #f0f0f0;
    border-radius: 4px;
    font-size: 11px;
    color: #428bca;
    text-decoration: none;
    margin-top: 8px;
}

/* Comment Form Inline */
.workman-detail-page .comment-form-inline {
    background: #f8f9fa;
    padding: 15px;
    border-top: 2px solid #f0ad4e;
}

.workman-detail-page .comment-input-wrapper textarea {
    width: 100%;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 12px;
    resize: vertical;
    font-size: 13px;
}

.workman-detail-page .comment-input-wrapper textarea:focus {
    border-color: #f0ad4e;
    outline: none;
    box-shadow: 0 0 0 3px rgba(240,173,78,0.15);
}

.workman-detail-page .comment-form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
}

.workman-detail-page .file-attach-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    background: #fff;
    border: 1px dashed #ccc;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    color: #666;
    margin: 0;
    transition: all 0.2s;
}

.workman-detail-page .file-attach-btn:hover {
    border-color: #f0ad4e;
    color: #f0ad4e;
}

.workman-detail-page .file-input-hidden {
    display: none;
}

.workman-detail-page .comment-form-actions .btn {
    padding: 8px 20px;
    border-radius: 6px;
    font-weight: 500;
}

/* =============================================================================
   SUBMISSION PANEL STYLES
   ============================================================================= */
.workman-detail-page .task-panel-success {
    border-left-color: #5cb85c;
}

.workman-detail-page .task-panel-success .task-panel-header {
    background: linear-gradient(135deg, #5cb85c 0%, #4cae4c 100%);
}

.workman-detail-page .file-count-badge {
    background: rgba(255,255,255,0.2);
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    margin-left: auto;
}

/* Submission items */
.workman-detail-page .submission-item {
    background: #f9f9f9;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #e8e8e8;
}

.workman-detail-page .submission-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.workman-detail-page .submission-user {
    display: flex;
    align-items: center;
    gap: 8px;
}

.workman-detail-page .submission-user i {
    font-size: 18px;
    color: #5cb85c;
}

.workman-detail-page .submission-time {
    font-size: 12px;
    color: #888;
}

.workman-detail-page .submission-description {
    background: #fff;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 12px;
    border-left: 3px solid #5cb85c;
    line-height: 1.6;
}

/* Submission files */
.workman-detail-page .submission-files {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.workman-detail-page .submission-file-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ddd;
    min-width: 200px;
    transition: all 0.3s;
}

.workman-detail-page .submission-file-item:hover {
    border-color: #5cb85c;
    box-shadow: 0 2px 8px rgba(92,184,92,0.15);
}

.workman-detail-page .file-thumbnail {
    width: 50px;
    height: 50px;
    border-radius: 6px;
    overflow: hidden;
}

.workman-detail-page .file-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.workman-detail-page .file-icon-box {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.workman-detail-page .file-icon-box i {
    font-size: 22px;
    color: #666;
}

.workman-detail-page .file-details {
    flex: 1;
    min-width: 0;
}

.workman-detail-page .file-details .file-name {
    display: block;
    font-weight: 500;
    color: #333;
    text-decoration: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.workman-detail-page .file-details .file-name:hover {
    color: #5cb85c;
}

.workman-detail-page .file-details .file-meta {
    display: block;
    font-size: 11px;
    color: #888;
    margin-top: 3px;
}

.workman-detail-page .btn-delete-file {
    background: none;
    border: none;
    color: #d9534f;
    cursor: pointer;
    padding: 5px;
    opacity: 0.6;
    transition: opacity 0.2s;
}

.workman-detail-page .btn-delete-file:hover {
    opacity: 1;
}

/* Submission form toggle button */
.workman-detail-page .submission-form-toggle {
    margin-top: 15px;
    text-align: center;
}

.workman-detail-page .submission-form-toggle .btn {
    font-weight: 500;
    padding: 8px 20px;
    border-radius: 5px;
    font-size: 13px;
}

/* Submission form */
.workman-detail-page .submission-form-wrapper {
    margin-top: 15px;
    padding: 20px;
    background: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    animation: slideDown 0.3s ease-out;
}

/* File upload zone */
.workman-detail-page .file-upload-zone {
    border: 2px dashed #ccc;
    border-radius: 10px;
    padding: 30px;
    text-align: center;
    transition: all 0.3s;
    background: #fafafa;
    cursor: pointer;
}

.workman-detail-page .file-upload-zone:hover,
.workman-detail-page .file-upload-zone.drag-over {
    border-color: #5cb85c;
    background: #f0fff0;
}

.workman-detail-page .file-upload-zone i {
    font-size: 48px;
    color: #ccc;
    display: block;
    margin-bottom: 10px;
}

.workman-detail-page .file-upload-zone p {
    margin: 0 0 5px;
    color: #666;
}

.workman-detail-page .file-upload-zone label {
    color: #5cb85c;
    cursor: pointer;
    font-weight: 600;
}

.workman-detail-page .file-upload-zone label:hover {
    text-decoration: underline;
}

.workman-detail-page .file-upload-zone small {
    color: #999;
    display: block;
}

.workman-detail-page .file-upload-zone input[type="file"] {
    display: none;
}

/* File preview list */
.workman-detail-page .file-preview-list {
    margin-top: 15px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.workman-detail-page .file-preview-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f5f5f5;
    padding: 10px 15px;
    border-radius: 6px;
    border: 1px solid #e0e0e0;
}

.workman-detail-page .file-preview-item i {
    font-size: 18px;
    color: #5cb85c;
}

.workman-detail-page .file-preview-name {
    flex: 1;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.workman-detail-page .file-preview-size {
    color: #888;
    font-size: 12px;
}

/* Form actions */
.workman-detail-page .form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    justify-content: flex-end;
}

.workman-detail-page .form-actions .btn {
    padding: 10px 24px;
    font-weight: 600;
    border-radius: 6px;
    transition: all 0.2s;
}

.workman-detail-page .form-actions .btn-success {
    background: linear-gradient(135deg, #5cb85c 0%, #4cae4c 100%);
    border: none;
    box-shadow: 0 2px 8px rgba(92,184,92,0.3);
}

.workman-detail-page .form-actions .btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(92,184,92,0.4);
}

.workman-detail-page .form-actions .btn-default {
    background: #f5f5f5;
    border: 1px solid #ddd;
    color: #666;
}

.workman-detail-page .form-actions .btn-default:hover {
    background: #e9e9e9;
    border-color: #bbb;
}

/* Submission Cards - COMPACT STYLE */
.submission-item-compact {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    margin-bottom: 10px;
    overflow: hidden;
    transition: all 0.2s;
}

.submission-item-compact:hover {
    border-color: #c0c0c0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.submission-header-compact {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 15px;
    background: #fafafa;
}

.submission-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 0 0 auto;
}

.submission-number {
    background: #e9ecef;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 11px;
    color: #495057;
    font-weight: 600;
}

.submission-files-inline {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    overflow-x: auto;
    padding: 0 10px;
}

.file-link-inline {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 13px;
    white-space: nowrap;
    color: #333;
    text-decoration: none;
    transition: all 0.2s;
}

.file-link-inline:hover {
    background: #f5f5f5;
    border-color: #bbb;
    color: #007bff;
    text-decoration: none;
}

.file-link-inline i {
    color: #666;
}

.submission-actions-compact {
    display: flex;
    gap: 5px;
}

.btn-toggle-desc,
.btn-delete-submission {
    background: transparent;
    border: 1px solid #ddd;
    padding: 4px 8px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    color: #666;
}

.btn-toggle-desc:hover {
    background: #f0f0f0;
    border-color: #bbb;
}

.btn-delete-submission:hover {
    background: #ffebee;
    border-color: #f44336;
    color: #f44336;
}

.btn-toggle-desc.expanded i {
    transform: rotate(180deg);
}

.submission-desc-expandable {
    padding: 15px;
    background: #f9f9f9;
    border-top: 1px solid #e9ecef;
    animation: slideDown 0.2s ease-out;
}

.submission-desc-content {
    background: #fff;
    padding: 12px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    white-space: pre-wrap;
    font-size: 14px;
    line-height: 1.6;
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
    }
    to {
        opacity: 1;
        max-height: 500px;
    }
}

/* Responsive */
@media (max-width: 991px) {
    .workman-detail-page .detail-sidebar {
        position: static;
        margin-top: 20px;
    }
}

@media (max-width: 767px) {
    .workman-detail-page .detail-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
    
    .workman-detail-page .detail-label {
        width: 100%;
    }
    
    .workman-detail-page .comment-form-actions {
        flex-direction: column;
        gap: 10px;
    }
    
    .workman-detail-page .comment-form-actions .btn {
        width: 100%;
    }
    
    .workman-detail-page .submission-files {
        flex-direction: column;
    }
    
    .workman-detail-page .submission-file-item {
        min-width: 100%;
    }
}
</style> -->

<!-- Load external JavaScript -->
<script src="{NV_BASE_SITEURL}themes/{TEMPLATE}/modules/workman/js/detail.js"></script>
<script>
// Initialize module with configuration
document.addEventListener('DOMContentLoaded', function() {
    WorkmanDetail.init({
        taskId: {TASK_ID},
        urls: {
            update: '{URL_UPDATE}',
            submission: '{URL_SUBMISSION}'
        }
    });
});

// Toggle submission description
function toggleSubmissionDesc(submissionId) {
    var descEl = document.getElementById('desc-' + submissionId);
    var btnEl = document.querySelector('[onclick="toggleSubmissionDesc(' + submissionId + ')"]');
    
    if (descEl.style.display === 'none') {
        descEl.style.display = 'block';
        btnEl.classList.add('expanded');
    } else {
        descEl.style.display = 'none';
        btnEl.classList.remove('expanded');
    }
}

// Toggle submission form
function toggleSubmissionForm() {
    var formWrapper = document.getElementById('submissionFormWrapper');
    var toggleBtn = document.querySelector('.submission-form-toggle');
    
    if (formWrapper.style.display === 'none') {
        formWrapper.style.display = 'block';
        if (toggleBtn) toggleBtn.style.display = 'none';
        // Initialize CKEditor for submission when form shows
        initSubmissionEditor();
    } else {
        formWrapper.style.display = 'none';
        if (toggleBtn) toggleBtn.style.display = 'block';
        // Reset form
        var form = document.getElementById('submissionForm');
        if (form) form.reset();
        var previewList = document.getElementById('filePreviewList');
        if (previewList) previewList.innerHTML = '';
    }
}

// Initialize CKEditor for submission description
window.submissionEditor = null;
function initSubmissionEditor() {
    var submissionTextarea = document.getElementById('submissionDescription');
    if (typeof ClassicEditor !== 'undefined' && submissionTextarea && !window.submissionEditor) {
        // Remove required and minlength - we'll validate manually
        submissionTextarea.removeAttribute('required');
        submissionTextarea.removeAttribute('minlength');
        
        ClassicEditor
        .create(submissionTextarea, {
            language: '{NV_LANG_INTERFACE}',
            toolbar: {
                items: [
                    'undo', 'redo', '|',
                    'bold', 'italic', 'underline', '|',
                    'link', 'bulletedList', 'numberedList', '|',
                    'blockQuote'
                ]
            },
            removePlugins: ['NVBox', 'ImageUpload']
        })
        .then(editor => {
            window.submissionEditor = editor;
            
            // Add form submit validation
            var submissionForm = document.getElementById('submissionForm');
            if (submissionForm) {
                submissionForm.addEventListener('submit', function(e) {
                    // Update textarea with editor content
                    submissionTextarea.value = editor.getData();
                    
                    // Get plain text for length check
                    var plainText = submissionTextarea.value.replace(/<[^>]*>/g, '').trim();
                    
                    if (plainText.length < 10) {
                        e.preventDefault();
                        alert('Vui lòng nhập mô tả kết quả (tối thiểu 10 ký tự)');
                        editor.focus();
                        return false;
                    }
                });
            }
        })
        .catch(error => console.error(error));
    }
}

// Initialize CKEditor for comment
window.commentEditor = null;
document.addEventListener('DOMContentLoaded', function() {
    var commentTextarea = document.getElementById('commentContent');
    if (typeof ClassicEditor !== 'undefined' && commentTextarea) {
        // Remove required attribute - we'll validate manually
        commentTextarea.removeAttribute('required');
        
        ClassicEditor
        .create(commentTextarea, {
            language: '{NV_LANG_INTERFACE}',
            toolbar: {
                items: [
                    'bold', 'italic', 'underline', '|',
                    'link', 'bulletedList', 'numberedList'
                ]
            },
            removePlugins: ['NVBox', 'ImageUpload']
        })
        .then(editor => {
            window.commentEditor = editor;
            
            // Add form submit validation
            var commentForm = commentTextarea.closest('form');
            if (commentForm) {
                commentForm.addEventListener('submit', function(e) {
                    // Update textarea with editor content
                    commentTextarea.value = editor.getData();
                    
                    // Check if empty
                    if (!commentTextarea.value.trim()) {
                        e.preventDefault();
                        alert('Vui lòng nhập nội dung bình luận');
                        editor.focus();
                        return false;
                    }
                });
            }
        })
        .catch(error => console.error(error));
    }
});
</script>
<!-- END: main -->
