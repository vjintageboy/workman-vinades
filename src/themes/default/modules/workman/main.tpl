<!-- BEGIN: main -->
<div class="workman-dashboard">
    <h2 class="page-header">
        <i class="fa fa-dashboard"></i> Dashboard công việc
    </h2>
    
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="panel panel-info">
                <div class="panel-body text-center">
                    <h2>{STATS.pending}</h2>
                    <p>Chờ nhận</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="panel panel-warning">
                <div class="panel-body text-center">
                    <h2>{STATS.doing}</h2>
                    <p>Đang làm</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="panel panel-primary">
                <div class="panel-body text-center">
                    <h2>{STATS.review}</h2>
                    <p>Chờ duyệt</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="panel panel-success">
                <div class="panel-body text-center">
                    <h2>{STATS.done}</h2>
                    <p>Hoàn thành</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Button -->
    <div style="margin-bottom: 20px;">
        <a href="{URL_LIST}" class="btn btn-primary">
            <i class="fa fa-list"></i> Xem tất cả công việc
        </a>
    </div>
    
    <div class="row">
        <!-- Pending Tasks -->
        <div class="col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-clock-o"></i> Công việc chờ nhận
                </div>
                <div class="panel-body">
                    <!-- BEGIN: pending_task -->
                    <div class="task-item" style="padding: 10px; border-bottom: 1px solid #eee;">
                        <a href="{PENDING.url_detail}">
                            <strong>{PENDING.title}</strong>
                        </a>
                        <!-- BEGIN: category -->
                        <span class="label" style="background-color: {PENDING.category_color}; margin-left: 5px;">{PENDING.category_title}</span>
                        <!-- END: category -->
                        <br>
                        <small class="text-muted">
                            <i class="fa fa-calendar"></i> Hạn: {PENDING.due_date_formatted}
                        </small>
                    </div>
                    <!-- END: pending_task -->
                    <!-- BEGIN: no_pending -->
                    <p class="text-muted text-center">Không có công việc chờ nhận</p>
                    <!-- END: no_pending -->
                </div>
            </div>
        </div>
        
        <!-- Doing Tasks -->
        <div class="col-md-6">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <i class="fa fa-spinner fa-spin"></i> Đang thực hiện
                </div>
                <div class="panel-body">
                    <!-- BEGIN: doing_task -->
                    <div class="task-item" style="padding: 10px; border-bottom: 1px solid #eee;">
                        <a href="{DOING.url_detail}">
                            <strong>{DOING.title}</strong>
                        </a>
                        <!-- BEGIN: overdue -->
                        <span class="label label-danger">Quá hạn!</span>
                        <!-- END: overdue -->
                        <br>
                        <small class="text-muted">
                            <i class="fa fa-calendar"></i> Hạn: {DOING.due_date_formatted}
                        </small>
                    </div>
                    <!-- END: doing_task -->
                    <!-- BEGIN: no_doing -->
                    <p class="text-muted text-center">Không có công việc đang làm</p>
                    <!-- END: no_doing -->
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Review Tasks -->
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <i class="fa fa-check-circle"></i> Chờ duyệt
                </div>
                <div class="panel-body">
                    <!-- BEGIN: review_task -->
                    <div class="task-item" style="padding: 10px; border-bottom: 1px solid #eee;">
                        <a href="{REVIEW.url_detail}">
                            <strong>{REVIEW.title}</strong>
                        </a>
                        <br>
                        <small class="text-muted">
                            <i class="fa fa-calendar"></i> Hạn: {REVIEW.due_date_formatted}
                        </small>
                    </div>
                    <!-- END: review_task -->
                    <!-- BEGIN: no_review -->
                    <p class="text-muted text-center">Không có công việc chờ duyệt</p>
                    <!-- END: no_review -->
                </div>
            </div>
        </div>
        
        <!-- Notifications -->
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bell"></i> Thông báo mới
                    <!-- BEGIN: unread_badge -->
                    <span class="badge">{UNREAD_COUNT}</span>
                    <!-- END: unread_badge -->
                </div>
                <div class="panel-body">
                    <!-- BEGIN: notification -->
                    <div class="notification-item" style="padding: 10px; border-bottom: 1px solid #eee;">
                        <a href="{NOTIF.url_detail}">
                            {NOTIF.message}
                        </a>
                        <br>
                        <small class="text-muted">{NOTIF.created_at_formatted}</small>
                    </div>
                    <!-- END: notification -->
                    <!-- BEGIN: no_notifications -->
                    <p class="text-muted text-center">Không có thông báo mới</p>
                    <!-- END: no_notifications -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: main -->
