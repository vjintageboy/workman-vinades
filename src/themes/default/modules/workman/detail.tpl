<!-- BEGIN: main -->
<div class="workman-detail">
    <!-- Header -->
    <div class="page-header clearfix" style="margin-top: 0; margin-bottom: 20px;">
        <div class="pull-right">
            <a href="{URL_LIST}" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Quay lại
            </a>
        </div>
        <h2 style="margin-top: 0;">{TASK.title}</h2>
    </div>
    
    <!-- Task Info Panel -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><i class="fa fa-info-circle"></i> Thông tin công việc</strong>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                <tbody>
                    <tr>
                        <th style="width: 150px;">Trạng thái</th>
                        <td><span class="label label-{TASK.status_class}">{TASK.status_text}</span></td>
                    </tr>
                    <tr>
                        <th>Độ ưu tiên</th>
                        <td><span class="label label-{TASK.priority_class}">{TASK.priority_text}</span></td>
                    </tr>
                    <tr>
                        <th>Danh mục</th>
                        <td><span class="label" style="background-color: {TASK.category_color};">{TASK.category_title}</span></td>
                    </tr>
                    <tr>
                        <th>Người giao</th>
                        <td>{TASK.creator_name}</td>
                    </tr>
                    <tr>
                        <th>Hạn chốt</th>
                        <td>
                            <span<!-- BEGIN: is_overdue --> class="text-danger"<!-- END: is_overdue -->>
                                {TASK.due_date_formatted}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tạo lúc</th>
                        <td>{TASK.created_at_formatted}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Description -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><i class="fa fa-file-text-o"></i> Mô tả chi tiết</strong>
        </div>
        <div class="panel-body">
            <div class="well well-sm" style="white-space: pre-wrap; word-wrap: break-word; min-height: 60px;">
                {TASK.description}
            </div>
        </div>
    </div>
    
    <!-- BEGIN: attachment_image -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><i class="fa fa-image"></i> Hình ảnh đính kèm</strong>
        </div>
        <div class="panel-body text-center">
            <a href="{TASK.attachment_url}" target="_blank" class="thumbnail" style="display: inline-block;">
                <img src="{TASK.attachment_url}" alt="{TASK.attachment_name}" class="img-responsive" style="max-height: 400px;">
            </a>
        </div>
    </div>
    <!-- END: attachment_image -->
    
    <!-- BEGIN: attachment_file -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><i class="fa fa-file"></i> File đính kèm</strong>
        </div>
        <div class="panel-body">
            <a href="{TASK.attachment_url}" target="_blank" class="btn btn-default">
                <i class="fa fa-download"></i> {TASK.attachment_name}
            </a>
        </div>
    </div>
    <!-- END: attachment_file -->
    
    <!-- Action Buttons -->
    <!-- BEGIN: action_accept -->
    <div class="text-center" style="margin-bottom: 20px;">
        <button type="button" class="btn btn-success btn-lg" onclick="updateStatus('doing');">
            <i class="fa fa-check"></i> Nhận việc
        </button>
    </div>
    <!-- END: action_accept -->
    
    <!-- BEGIN: action_review -->
    <div class="text-center" style="margin-bottom: 20px;">
        <button type="button" class="btn btn-primary btn-lg" onclick="updateStatus('review');">
            <i class="fa fa-paper-plane"></i> Gửi duyệt
        </button>
    </div>
    <!-- END: action_review -->
    
    <!-- Comments Section -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><i class="fa fa-comments"></i> Bình luận</strong>
        </div>
        <div class="panel-body">
            <!-- BEGIN: comment -->
            <div class="media" style="padding: 15px 0; border-bottom: 1px solid #eee;">
                <div class="media-body">
                    <h5 class="media-heading">
                        <strong>{COMMENT.user_fullname}</strong>
                        <small class="pull-right text-muted">{COMMENT.created_at_formatted}</small>
                    </h5>
                    <div style="margin-top: 10px;">{COMMENT.content}</div>
                    <!-- BEGIN: attachment -->
                    <div style="margin-top: 10px;">
                        <a href="{COMMENT.attachment_url}" target="_blank" class="btn btn-xs btn-default">
                            <i class="fa fa-paperclip"></i> {COMMENT.attachment_name}
                        </a>
                    </div>
                    <!-- END: attachment -->
                </div>
            </div>
            <!-- END: comment -->
            
            <!-- BEGIN: no_comments -->
            <div class="text-center text-muted" style="padding: 30px 0;">
                <i class="fa fa-comments-o fa-3x" style="opacity: 0.3;"></i>
                <p style="margin-top: 10px;">Chưa có bình luận nào.</p>
            </div>
            <!-- END: no_comments -->
        </div>
    </div>
    
    <!-- Comment Form -->
    <!-- BEGIN: comment_form -->
    <div class="panel panel-info">
        <div class="panel-heading">
            <strong><i class="fa fa-reply"></i> Thêm bình luận</strong>
        </div>
        <div class="panel-body">
            <form method="post" action="{URL_COMMENT}" enctype="multipart/form-data">
                <input type="hidden" name="work_id" value="{TASK_ID}">
                <div class="form-group">
                    <textarea name="content" class="form-control" rows="4" placeholder="Nhập nội dung bình luận..." required></textarea>
                </div>
                <div class="form-group">
                    <label><i class="fa fa-paperclip"></i> Đính kèm file (tùy chọn):</label>
                    <input type="file" name="attachment" class="form-control">
                </div>
                <div class="form-group">
                    <button type="submit" name="submit_comment" class="btn btn-primary btn-block">
                        <i class="fa fa-send"></i> Gửi bình luận
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- END: comment_form -->
    
    <!-- Activity Log -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><i class="fa fa-history"></i> Lịch sử hoạt động</strong>
        </div>
        <div class="panel-body">
            <!-- BEGIN: log -->
            <div style="padding: 10px 0; border-bottom: 1px solid #eee;">
                <strong>{LOG.user_fullname}</strong>
                <span class="text-info">{LOG.action_text}</span>
                <br>
                <small class="text-muted">
                    <i class="fa fa-clock-o"></i> {LOG.created_at_formatted}
                </small>
            </div>
            <!-- END: log -->
            
            <!-- BEGIN: no_logs -->
            <div class="text-center text-muted" style="padding: 20px 0;">
                Chưa có hoạt động nào.
            </div>
            <!-- END: no_logs -->
        </div>
    </div>
</div>

<script>
function updateStatus(newStatus) {
    if (!confirm('Bạn có chắc chắn muốn cập nhật trạng thái?')) return;
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '{URL_UPDATE}', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.error === 0) {
                    alert(res.message);
                    location.reload();
                } else {
                    alert('Lỗi: ' + res.message);
                }
            } catch (e) {
                alert('Có lỗi xảy ra');
            }
        }
    };
    xhr.send('id={TASK_ID}&status=' + newStatus);
}
</script>
<!-- END: main -->
