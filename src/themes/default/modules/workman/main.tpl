<!-- BEGIN: main -->
<div class="workman-dashboard">
    <!-- Header với greeting -->
    <div class="dashboard-header">
        <h3><i class="fa fa-tachometer"></i> Dashboard công việc</h3>
        <p class="text-muted">Chào mừng bạn quay trở lại! Đây là tổng quan công việc của bạn.</p>
    </div>
    
    <!-- Progress + Stats Row -->
    <div class="row">
        <!-- Progress Ring -->
        <div class="col-xs-24 col-sm-8">
            <div class="progress-card">
                <div class="progress-ring-container">
                    <svg class="progress-ring" viewBox="0 0 120 120">
                        <circle class="progress-ring-bg" cx="60" cy="60" r="54"/>
                        <circle class="progress-ring-fill" cx="60" cy="60" r="54" 
                            style="stroke-dasharray: 339.292; stroke-dashoffset: calc(339.292 - (339.292 * {PROGRESS_PERCENT}) / 100);"/>
                    </svg>
                    <div class="progress-text">
                        <span class="progress-value">{PROGRESS_PERCENT}%</span>
                        <span class="progress-label">Hoàn thành</span>
                    </div>
                </div>
                <div class="progress-info">
                    <span><strong>{STATS.done}</strong> / {TOTAL_TASKS} công việc</span>
                </div>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="col-xs-24 col-sm-16">
            <div class="row stats-row">
                <div class="col-xs-12 col-sm-12">
                    <div class="stat-card stat-pending">
                        <div class="stat-icon">
                            <i class="fa fa-hourglass-half"></i>
                        </div>
                        <div class="stat-content">
                            <h2>{STATS.pending}</h2>
                            <span>Chờ nhận</span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <div class="stat-card stat-doing">
                        <div class="stat-icon">
                            <i class="fa fa-cogs"></i>
                        </div>
                        <div class="stat-content">
                            <h2>{STATS.doing}</h2>
                            <span>Đang làm</span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <div class="stat-card stat-review">
                        <div class="stat-icon">
                            <i class="fa fa-eye"></i>
                        </div>
                        <div class="stat-content">
                            <h2>{STATS.review}</h2>
                            <span>Chờ duyệt</span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <div class="stat-card stat-done">
                        <div class="stat-icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h2>{STATS.done}</h2>
                            <span>Hoàn thành</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{URL_LIST}" class="quick-action-btn">
            <i class="fa fa-list-ul"></i>
            <span>Tất cả công việc</span>
        </a>
        <a href="{URL_LIST}&status=pending" class="quick-action-btn">
            <i class="fa fa-inbox"></i>
            <span>Chờ nhận</span>
        </a>
        <a href="{URL_LIST}&status=doing" class="quick-action-btn">
            <i class="fa fa-play-circle"></i>
            <span>Đang làm</span>
        </a>
        <a href="{URL_LIST}&status=done" class="quick-action-btn">
            <i class="fa fa-check"></i>
            <span>Đã xong</span>
        </a>
    </div>
    
    <!-- Upcoming Deadlines - Full Width -->
    <div class="task-panel task-panel-danger">
        <div class="task-panel-header">
            <div class="panel-title-icon"><i class="fa fa-fire"></i></div>
            <h4>Deadline sắp tới (3 ngày)</h4>
        </div>
        <div class="task-panel-body deadline-list">
            <!-- BEGIN: deadline -->
            <div class="deadline-item">
                <div class="deadline-time">
                    <!-- BEGIN: overdue -->
                    <span class="deadline-badge overdue">Quá hạn</span>
                    <!-- END: overdue -->
                    <!-- BEGIN: today -->
                    <span class="deadline-badge today">Hôm nay</span>
                    <!-- END: today -->
                    <span class="deadline-date">{DEADLINE.due_date_formatted}</span>
                </div>
                <div class="deadline-info">
                    <a href="{DEADLINE.url_detail}">{DEADLINE.title}</a>
                    <span class="deadline-status">{DEADLINE.status}</span>
                </div>
            </div>
            <!-- END: deadline -->
            <!-- BEGIN: no_deadlines -->
            <div class="empty-state small">
                <i class="fa fa-calendar-check-o"></i>
                <p>Không có deadline gấp</p>
            </div>
            <!-- END: no_deadlines -->
        </div>
    </div>
    
    <!-- Task Lists - 3 Columns -->
    <div class="row">
        <!-- Pending Tasks -->
        <div class="col-xs-24 col-sm-8">
            <div class="task-panel task-panel-info">
                <div class="task-panel-header">
                    <div class="panel-title-icon"><i class="fa fa-clock-o"></i></div>
                    <h4>Chờ nhận</h4>
                </div>
                <div class="task-panel-body">
                    <!-- BEGIN: pending_task -->
                    <div class="task-item-modern">
                        <div class="task-priority-bar priority-medium"></div>
                        <div class="task-content">
                            <a href="{PENDING.url_detail}" class="task-title">
                                {PENDING.title}
                            </a>
                            <!-- BEGIN: category -->
                            <span class="task-category" style="background-color: {PENDING.category_color};">{PENDING.category_title}</span>
                            <!-- END: category -->
                            <div class="task-meta">
                                <span><i class="fa fa-calendar-o"></i> {PENDING.due_date_formatted}</span>
                            </div>
                        </div>
                        <div class="task-action">
                            <a href="{PENDING.url_detail}" class="btn btn-xs btn-info"><i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <!-- END: pending_task -->
                    <!-- BEGIN: no_pending -->
                    <div class="empty-state">
                        <i class="fa fa-inbox"></i>
                        <p>Chưa có việc</p>
                    </div>
                    <!-- END: no_pending -->
                </div>
            </div>
        </div>
        
        <!-- Doing Tasks -->
        <div class="col-xs-24 col-sm-8">
            <div class="task-panel task-panel-warning">
                <div class="task-panel-header">
                    <div class="panel-title-icon"><i class="fa fa-spinner fa-spin"></i></div>
                    <h4>Đang làm</h4>
                </div>
                <div class="task-panel-body">
                    <!-- BEGIN: doing_task -->
                    <div class="task-item-modern">
                        <div class="task-priority-bar priority-urgent"></div>
                        <div class="task-content">
                            <a href="{DOING.url_detail}" class="task-title">
                                {DOING.title}
                            </a>
                            <!-- BEGIN: overdue -->
                            <span class="badge-overdue"><i class="fa fa-exclamation-triangle"></i> Quá hạn!</span>
                            <!-- END: overdue -->
                            <div class="task-meta">
                                <span><i class="fa fa-calendar-o"></i> {DOING.due_date_formatted}</span>
                            </div>
                        </div>
                        <div class="task-action">
                            <a href="{DOING.url_detail}" class="btn btn-xs btn-warning"><i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <!-- END: doing_task -->
                    <!-- BEGIN: no_doing -->
                    <div class="empty-state">
                        <i class="fa fa-coffee"></i>
                        <p>Chưa có việc</p>
                    </div>
                    <!-- END: no_doing -->
                </div>
            </div>
        </div>
        
        <!-- Review Tasks -->
        <div class="col-xs-24 col-sm-8">
            <div class="task-panel task-panel-primary">
                <div class="task-panel-header">
                    <div class="panel-title-icon"><i class="fa fa-paper-plane"></i></div>
                    <h4>Chờ duyệt</h4>
                </div>
                <div class="task-panel-body">
                    <!-- BEGIN: review_task -->
                    <div class="task-item-modern">
                        <div class="task-priority-bar priority-high"></div>
                        <div class="task-content">
                            <a href="{REVIEW.url_detail}" class="task-title">
                                {REVIEW.title}
                            </a>
                            <div class="task-meta">
                                <span><i class="fa fa-calendar-o"></i> {REVIEW.due_date_formatted}</span>
                            </div>
                        </div>
                        <div class="task-action">
                            <a href="{REVIEW.url_detail}" class="btn btn-xs btn-primary"><i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <!-- END: review_task -->
                    <!-- BEGIN: no_review -->
                    <div class="empty-state">
                        <i class="fa fa-clipboard"></i>
                        <p>Chưa có việc</p>
                    </div>
                    <!-- END: no_review -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activity & Notifications - 2 Columns -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-xs-24 col-sm-12">
            <div class="task-panel task-panel-success">
                <div class="task-panel-header">
                    <div class="panel-title-icon"><i class="fa fa-history"></i></div>
                    <h4>Hoạt động gần đây</h4>
                </div>
                <div class="task-panel-body">
                    <!-- BEGIN: activity -->
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div class="activity-content">
                            <div>
                                <strong class="activity-user">{ACTIVITY.user_fullname}</strong>
                                <span class="activity-action">{ACTIVITY.action_text}:</span>
                                <a href="{ACTIVITY.url_detail}" class="activity-task">{ACTIVITY.work_title}</a>
                            </div>
                            <small class="activity-time"><i class="fa fa-clock-o"></i> {ACTIVITY.time_ago}</small>
                        </div>
                    </div>
                    <!-- END: activity -->
                    <!-- BEGIN: no_activities -->
                    <div class="empty-state small">
                        <i class="fa fa-clock-o"></i>
                        <p>Chưa có hoạt động</p>
                    </div>
                    <!-- END: no_activities -->
                </div>
            </div>
        </div>
        
        <!-- Notifications -->
        <div class="col-xs-24 col-sm-12">
            <div class="task-panel task-panel-default">
                <div class="task-panel-header">
                    <div class="panel-title-icon"><i class="fa fa-bell"></i></div>
                    <h4>Thông báo mới</h4>
                    <!-- BEGIN: unread_badge -->
                    <span class="notification-count">{UNREAD_COUNT}</span>
                    <!-- END: unread_badge -->
                </div>
                <div class="task-panel-body">
                    <!-- BEGIN: notification -->
                    <div class="notification-item-modern">
                        <div class="notif-icon">
                            <i class="fa fa-info-circle"></i>
                        </div>
                        <div class="notif-content">
                            <a href="{NOTIF.url_detail}">{NOTIF.message}</a>
                            <small>{NOTIF.created_at_formatted}</small>
                        </div>
                    </div>
                    <!-- END: notification -->
                    <!-- BEGIN: no_notifications -->
                    <div class="empty-state">
                        <i class="fa fa-bell-slash"></i>
                        <p>Không có thông báo mới</p>
                    </div>
                    <!-- END: no_notifications -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: main -->
