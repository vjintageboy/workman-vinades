<!-- BEGIN: main -->
<div class="news_column panel panel-default">
    <div class="panel-body">
        <!-- Header -->
        <div class="page-header" style="margin-top: 0;">
            <div class="pull-right">
                <a href="{URL_LIST}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
            <h1>{TASK.title}</h1>
        </div>
        
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-8">
                <!-- Task Info -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong><i class="fa fa-info-circle"></i> Thông tin công việc</strong>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <p><strong>Trạng thái:</strong> 
                                    <span class="label label-{TASK.status_class}">{TASK.status_text}</span>
                                </p>
                                <p><strong>Độ ưu tiên:</strong> 
                                    <span class="label label-{TASK.priority_class}">{TASK.priority_text}</span>
                                </p>
                                <p><strong>Danh mục:</strong> 
                                    <span class="label" style="background-color: {TASK.category_color};">{TASK.category_title}</span>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <p><strong>Người giao:</strong> {TASK.creator_name}</p>
                                <p><strong>Hạn chót:</strong> 
                                    <span<!-- BEGIN: is_overdue --> class="text-danger"<!-- END: is_overdue -->>
                                        {TASK.due_date_formatted}
                                    </span>
                                </p>
                                <p><strong>Tạo lúc:</strong> {TASK.created_at_formatted}</p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="task-description">
                            <h4 class="margin-bottom">Mô tả chi tiết:</h4>
                            <div class="well well-sm">
                                {TASK.description}
                            </div>
                        </div>
                        
                        <!-- BEGIN: attachment_image -->
                        <hr>
                        <div class="task-attachment">
                            <h4 class="margin-bottom">Hình ảnh đính kèm:</h4>
                            <div class="text-center">
                                <a href="{TASK.attachment_url}" target="_blank" class="thumbnail" style="display: inline-block;">
                                    <img src="{TASK.attachment_url}" alt="{TASK.attachment_name}" class="img-responsive" style="max-height: 400px;">
                                </a>
                            </div>
                        </div>
                        <!-- END: attachment_image -->
                        
                        <!-- BEGIN: attachment_file -->
                        <hr>
                        <div class="task-attachment">
                            <h4 class="margin-bottom">File đính kèm:</h4>
                            <a href="{TASK.attachment_url}" target="_blank" class="btn btn-default">
                                <i class="fa fa-download"></i> {TASK.attachment_name}
                            </a>
                        </div>
                        <!-- END: attachment_file -->
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <!-- BEGIN: action_accept -->
                <div class="text-center margin-bottom-lg">
                    <button type="button" class="btn btn-success btn-lg" onclick="updateStatus('doing');">
                        <i class="fa fa-check"></i> Nhận việc
                    </button>
                </div>
                <!-- END: action_accept -->
                
                <!-- BEGIN: action_review -->
                <div class="text-center margin-bottom-lg">
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
                        <div class="media margin-bottom" style="padding-bottom: 15px; border-bottom: 1px solid #eee;">
                            <div class="media-body">
                                <h4 class="media-heading">
                                    {COMMENT.user_fullname}
                                    <small class="pull-right text-muted">{COMMENT.created_at_formatted}</small>
                                </h4>
                                <div>{COMMENT.content}</div>
                                <!-- BEGIN: attachment -->
                                <div class="margin-top">
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
                            <div class="margin-bottom">
                                <textarea name="content" class="form-control" rows="4" placeholder="Nhập nội dung bình luận..." required style="width: 100%;"></textarea>
                            </div>
                            <div class="margin-bottom">
                                <label><i class="fa fa-paperclip"></i> Đính kèm file (tùy chọn):</label>
                                <input type="file" name="attachment" class="form-control" style="width: 100%;">
                            </div>
                            <div>
                                <button type="submit" name="submit_comment" class="btn btn-primary btn-lg" style="width: 100%;">
                                    <i class="fa fa-send"></i> Gửi bình luận
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- END: comment_form -->
            </div>
            
            <!-- Sidebar - Activity Log -->
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong><i class="fa fa-history"></i> Lịch sử hoạt động</strong>
                    </div>
                    <div class="panel-body" style="max-height: 600px; overflow-y: auto;">
                        <!-- BEGIN: log -->
                        <div class="margin-bottom" style="padding-bottom: 10px; border-bottom: 1px solid #eee;">
                            <strong>{LOG.user_fullname}</strong>
                            <p class="text-info margin-bottom-sm">{LOG.action_text}</p>
                            <small class="text-muted">
                                <i class="fa fa-clock-o"></i> {LOG.created_at_formatted}
                            </small>
                        </div>
                        <!-- END: log -->
                    </div>
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
