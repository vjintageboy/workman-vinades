<!-- BEGIN: main -->
<div class="workman-dashboard">
    <h3 class="page-header" style="margin-top: 0;">
        <i class="fa fa-dashboard"></i> Dashboard công việc
    </h3>
    
    <!-- Stats -->
    <div class="row">
        <div class="col-xs-6 col-sm-3">
            <div class="panel panel-info text-center">
                <div class="panel-body">
                    <h2 style="margin: 5px 0; font-size: 28px;">{STATS.pending}</h2>
                    <small>Chờ nhận</small>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3">
            <div class="panel panel-warning text-center">
                <div class="panel-body">
                    <h2 style="margin: 5px 0; font-size: 28px;">{STATS.doing}</h2>
                    <small>Đang làm</small>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3">
            <div class="panel panel-primary text-center">
                <div class="panel-body">
                    <h2 style="margin: 5px 0; font-size: 28px;">{STATS.review}</h2>
                    <small>Chờ duyệt</small>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3">
            <div class="panel panel-success text-center">
                <div class="panel-body">
                    <h2 style="margin: 5px 0; font-size: 28px;">{STATS.done}</h2>
                    <small>Hoàn thành</small>
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
    
    <!-- Pending Tasks -->
    <div class="panel panel-info">
        <div class="panel-heading">
            <i class="fa fa-clock-o"></i> Công việc chờ nhận
        </div>
        <div class="panel-body">
            <!-- BEGIN: pending_task -->
            <div class="task-item">
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
            <p class="text-muted text-center" style="margin: 0;">Không có công việc chờ nhận</p>
            <!-- END: no_pending -->
        </div>
    </div>
    
    <!-- Doing Tasks -->
    <div class="panel panel-warning">
        <div class="panel-heading">
            <i class="fa fa-spinner fa-spin"></i> Đang thực hiện
        </div>
        <div class="panel-body">
            <!-- BEGIN: doing_task -->
            <div class="task-item">
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
            <p class="text-muted text-center" style="margin: 0;">Không có công việc đang làm</p>
            <!-- END: no_doing -->
        </div>
    </div>
    
    <!-- Review Tasks -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <i class="fa fa-check-circle"></i> Chờ duyệt
        </div>
        <div class="panel-body">
            <!-- BEGIN: review_task -->
            <div class="task-item">
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
            <p class="text-muted text-center" style="margin: 0;">Không có công việc chờ duyệt</p>
            <!-- END: no_review -->
        </div>
    </div>
    
    <!-- Notifications -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-bell"></i> Thông báo mới
            <!-- BEGIN: unread_badge -->
            <span class="badge">{UNREAD_COUNT}</span>
            <!-- END: unread_badge -->
        </div>
        <div class="panel-body">
            <!-- BEGIN: notification -->
            <div class="notification-item">
                <a href="{NOTIF.url_detail}">
                    {NOTIF.message}
                </a>
                <br>
                <small class="text-muted">{NOTIF.created_at_formatted}</small>
            </div>
            <!-- END: notification -->
            <!-- BEGIN: no_notifications -->
            <p class="text-muted text-center" style="margin: 0;">Không có thông báo mới</p>
            <!-- END: no_notifications -->
        </div>
    </div>
</div>
<!-- END: main -->
