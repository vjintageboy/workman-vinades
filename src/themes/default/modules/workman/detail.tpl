<!-- BEGIN: main -->
<div class="workman-detail">
    <!-- Header -->
    <div class="page-header">
        <div class="pull-right">
            <a href="{URL_LIST}" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Quay lại
            </a>
        </div>
        <h2>{TASK.title}</h2>
    </div>
    
    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Task Info -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-info-circle"></i> Thông tin công việc
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Trạng thái:</strong> 
                                <span class="label label-{TASK.status_class}">{TASK.status_text}</span>
                            </p>
                            <p><strong>Độ ưu tiên:</strong> 
                                <span class="text-{TASK.priority_class}">{TASK.priority_text}</span>
                            </p>
                            <p><strong>Danh mục:</strong> 
                                <span class="label" style="background-color: {TASK.category_color};">{TASK.category_title}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Người giao:</strong> {TASK.creator_name}</p>
                            <p><strong>Hạn chót:</strong> {TASK.due_date_formatted}</p>
                            <p><strong>Tạo lúc:</strong> {TASK.created_at_formatted}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="task-description">
                        <strong>Mô tả:</strong>
                        <div style="margin-top: 10px; padding: 15px; background: #f9f9f9; border-radius: 4px;">
                            {TASK.description}
                        </div>
                    </div>
                    
                    <!-- BEGIN: attachment_image -->
                    <hr>
                    <div class="task-attachment">
                        <strong>Hình ảnh đính kèm:</strong>
                        <div style="margin-top: 10px;">
                            <a href="{TASK.attachment_url}" target="_blank">
                                <img src="{TASK.attachment_url}" style="max-width: 100%; max-height: 400px; border: 1px solid #ddd; border-radius: 4px;">
                            </a>
                        </div>
                    </div>
                    <!-- END: attachment_image -->
                    
                    <!-- BEGIN: attachment_file -->
                    <hr>
                    <div class="task-attachment">
                        <strong>File đính kèm:</strong>
                        <a href="{TASK.attachment_url}" target="_blank" class="btn btn-sm btn-default" style="margin-left: 10px;">
                            <i class="fa fa-download"></i> {TASK.attachment_name}
                        </a>
                    </div>
                    <!-- END: attachment_file -->
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <!-- BEGIN: action_accept -->
                    <button type="button" class="btn btn-success btn-lg" onclick="updateStatus('doing');">
                        <i class="fa fa-check"></i> Nhận việc
                    </button>
                    <!-- END: action_accept -->
                    
                    <!-- BEGIN: action_review -->
                    <button type="button" class="btn btn-primary btn-lg" onclick="updateStatus('review');">
                        <i class="fa fa-paper-plane"></i> Gửi duyệt
                    </button>
                    <!-- END: action_review -->
                </div>
            </div>
            
            <!-- Comments -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-comments"></i> Bình luận
                </div>
                <div class="panel-body">
                    <!-- BEGIN: comment -->
                    <div class="comment-item" style="padding: 15px; border-bottom: 1px solid #eee;">
                        <strong>{COMMENT.user_fullname}</strong>
                        <small class="text-muted pull-right">{COMMENT.created_at_formatted}</small>
                        <div style="margin-top: 10px;">{COMMENT.content}</div>
                        <!-- BEGIN: attachment -->
                        <div style="margin-top: 10px;">
                            <a href="{COMMENT.attachment_url}" target="_blank" class="btn btn-xs btn-default">
                                <i class="fa fa-paperclip"></i> {COMMENT.attachment_name}
                            </a>
                        </div>
                        <!-- END: attachment -->
                    </div>
                    <!-- END: comment -->
                    
                    <!-- BEGIN: no_comments -->
                    <p class="text-muted text-center">Chưa có bình luận nào.</p>
                    <!-- END: no_comments -->
                </div>
            </div>
            
            <!-- Comment Form -->
            <!-- BEGIN: comment_form -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-reply"></i> Thêm bình luận
                </div>
                <div class="panel-body">
                    <form method="post" action="{URL_COMMENT}" enctype="multipart/form-data">
                        <input type="hidden" name="work_id" value="{TASK_ID}">
                        <div class="form-group">
                            <textarea name="content" class="form-control" rows="3" placeholder="Nhập bình luận..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label><i class="fa fa-paperclip"></i> Đính kèm file (tùy chọn):</label>
                            <input type="file" name="attachment" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-send"></i> Gửi bình luận
                        </button>
                    </form>
                </div>
            </div>
            <!-- END: comment_form -->
        </div>
        
        <!-- Sidebar - Activity Log -->
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-history"></i> Lịch sử hoạt động
                </div>
                <div class="panel-body" style="max-height: 500px; overflow-y: auto;">
                    <ul class="list-unstyled">
                        <!-- BEGIN: log -->
                        <li style="padding: 10px 0; border-bottom: 1px solid #eee;">
                            <strong>{LOG.user_fullname}</strong>
                            <br>
                            <span class="text-info">{LOG.action_text}</span>
                            <br>
                            <small class="text-muted"><i class="fa fa-clock-o"></i> {LOG.created_at_formatted}</small>
                        </li>
                        <!-- END: log -->
                    </ul>
                </div>
            </div>
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
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
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
        }
    };
    xhr.send('id={TASK_ID}&status=' + newStatus);
}
</script>
<!-- END: main -->
