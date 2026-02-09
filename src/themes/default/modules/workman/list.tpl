<!-- BEGIN: main -->
<div class="workman-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <h3><i class="fa fa-tasks"></i> Công việc của tôi</h3>
            <p class="text-muted">Danh sách tất cả công việc được giao cho bạn</p>
        </div>
        <a href="{URL_DASHBOARD}" class="btn btn-default">
            <i class="fa fa-dashboard"></i> Dashboard
        </a>
    </div>
    
    <!-- Task List -->
    <div class="task-panel task-panel-primary">
        <div class="task-panel-header">
            <div class="panel-title-icon"><i class="fa fa-clipboard"></i></div>
            <h4>Danh sách công việc</h4>
            <span class="notification-count">{TOTAL}</span>
        </div>
        <div class="task-panel-body">
            <!-- BEGIN: task -->
            <div class="task-item-card">
                <div class="task-priority-indicator priority-{TASK.priority}"></div>
                <div class="task-card-content">
                    <div class="task-card-header">
                        <a href="{TASK.url_detail}" class="task-title-link">
                            {TASK.title}
                        </a>
                        <div class="task-badges">
                            <!-- BEGIN: overdue -->
                            <span class="badge-overdue"><i class="fa fa-exclamation-triangle"></i> Quá hạn</span>
                            <!-- END: overdue -->
                            <span class="task-status status-{TASK.status}">{TASK.status_text}</span>
                        </div>
                    </div>
                    <div class="task-card-meta">
                        <!-- BEGIN: category -->
                        <span class="task-category" style="background-color: {TASK.category_color};">
                            <i class="fa fa-folder"></i> {TASK.category_title}
                        </span>
                        <!-- END: category -->
                        <span class="task-priority priority-text-{TASK.priority}">
                            <i class="fa fa-flag"></i> {TASK.priority_text}
                        </span>
                        <!-- BEGIN: due_date -->
                        <span class="task-due-date">
                            <i class="fa fa-calendar-o"></i> {TASK.due_date_formatted}
                        </span>
                        <!-- END: due_date -->
                    </div>
                </div>
                <div class="task-card-action">
                    <a href="{TASK.url_detail}" class="btn btn-sm btn-primary">
                        <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <!-- END: task -->
            
            <!-- BEGIN: no_tasks -->
            <div class="empty-state">
                <i class="fa fa-inbox"></i>
                <p>Không có công việc nào</p>
            </div>
            <!-- END: no_tasks -->
        </div>
    </div>
    
    <!-- BEGIN: pagination -->
    <div class="workman-pagination">
        {GENERATE_PAGE}
    </div>
    <!-- END: pagination -->
</div>

<style>
/* Task Item Card */
.task-item-card {
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    border: 1px solid #eee;
}

.task-item-card:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #3498db;
}

.task-priority-indicator {
    width: 5px;
    height: 60px;
    border-radius: 3px;
    margin-right: 15px;
    flex-shrink: 0;
}

.task-priority-indicator.priority-low { background: linear-gradient(180deg, #95a5a6, #7f8c8d); }
.task-priority-indicator.priority-normal { background: linear-gradient(180deg, #3498db, #2980b9); }
.task-priority-indicator.priority-high { background: linear-gradient(180deg, #e67e22, #d35400); }
.task-priority-indicator.priority-urgent { background: linear-gradient(180deg, #e74c3c, #c0392b); }

.task-card-content {
    flex: 1;
    min-width: 0;
}

.task-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    flex-wrap: wrap;
    gap: 8px;
}

.task-title-link {
    font-size: 15px;
    font-weight: 600;
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.2s;
}

.task-title-link:hover {
    color: #3498db;
}

.task-badges {
    display: flex;
    align-items: center;
    gap: 8px;
}

.task-status {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.task-status.status-pending { background: #e8f4fd; color: #3498db; }
.task-status.status-doing { background: #fef5e7; color: #e67e22; }
.task-status.status-review { background: #f4e6f8; color: #9b59b6; }
.task-status.status-done { background: #e8f8f0; color: #27ae60; }
.task-status.status-draft { background: #f0f0f0; color: #7f8c8d; }
.task-status.status-cancelled { background: #fbe9e7; color: #e74c3c; }

.badge-overdue {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: #fff;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.task-card-meta {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.task-card-meta span {
    font-size: 12px;
    color: #7f8c8d;
    display: flex;
    align-items: center;
    gap: 5px;
}

.task-card-meta .task-category {
    color: #fff;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
}

.priority-text-low { color: #95a5a6; }
.priority-text-normal { color: #3498db; }
.priority-text-high { color: #e67e22; }
.priority-text-urgent { color: #e74c3c; font-weight: 600; }

.task-card-action {
    margin-left: 15px;
}

.task-card-action .btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

/* Pagination */
.workman-pagination {
    text-align: center;
    padding: 20px 0;
}

.workman-pagination .pagination {
    display: inline-flex;
    gap: 5px;
}

.workman-pagination .pagination li a,
.workman-pagination .pagination li span {
    border-radius: 8px;
    padding: 8px 14px;
    border: none;
    background: #f8f9fa;
    color: #555;
    transition: all 0.2s;
}

.workman-pagination .pagination li.active span,
.workman-pagination .pagination li a:hover {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: #fff;
}

/* Header with dashboard button */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.dashboard-header .header-content {
    flex: 1;
}

.dashboard-header .btn {
    border-radius: 8px;
    padding: 10px 20px;
}
</style>
<!-- END: main -->
