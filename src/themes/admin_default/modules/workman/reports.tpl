<!-- BEGIN: main -->
<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-bar-chart"></i> Thống kê & Báo cáo
        <a href="{URL_BACK}" class="btn btn-xs btn-default pull-right">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-info" style="margin-bottom: 0;">
            <div class="panel-body text-center">
                <h2 style="margin: 5px 0;">{STATS.total}</h2>
                <small>Tổng</small>
            </div>
        </div>
    </div>
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-default" style="margin-bottom: 0;">
            <div class="panel-body text-center">
                <h2 style="margin: 5px 0;">{STATS.draft}</h2>
                <small>Bản nháp</small>
            </div>
        </div>
    </div>
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-info" style="margin-bottom: 0;">
            <div class="panel-body text-center">
                <h2 style="margin: 5px 0;">{STATS.pending}</h2>
                <small>Chờ xử lý</small>
            </div>
        </div>
    </div>
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-warning" style="margin-bottom: 0;">
            <div class="panel-body text-center">
                <h2 style="margin: 5px 0;">{STATS.doing}</h2>
                <small>Đang làm</small>
            </div>
        </div>
    </div>
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-primary" style="margin-bottom: 0;">
            <div class="panel-body text-center">
                <h2 style="margin: 5px 0;">{STATS.review}</h2>
                <small>Chờ duyệt</small>
            </div>
        </div>
    </div>
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-success" style="margin-bottom: 0;">
            <div class="panel-body text-center">
                <h2 style="margin: 5px 0;">{STATS.done}</h2>
                <small>Hoàn thành</small>
            </div>
        </div>
    </div>
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-danger" style="margin-bottom: 0;">
            <div class="panel-body text-center">
                <h2 style="margin: 5px 0;">{STATS.cancelled}</h2>
                <small>Đã hủy</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Stats by User -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-users"></i> Theo người thực hiện
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Người thực hiện</th>
                            <th class="text-center">Pending</th>
                            <th class="text-center">Doing</th>
                            <th class="text-center">Review</th>
                            <th class="text-center">Done</th>
                            <th class="text-center">Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- BEGIN: user_stat_row -->
                        <tr>
                            <td><strong>{USER_STAT.fullname}</strong></td>
                            <td class="text-center"><span class="badge badge-info">{USER_STAT.pending}</span></td>
                            <td class="text-center"><span class="badge badge-warning">{USER_STAT.doing}</span></td>
                            <td class="text-center"><span class="badge badge-primary">{USER_STAT.review}</span></td>
                            <td class="text-center"><span class="badge badge-success">{USER_STAT.done}</span></td>
                            <td class="text-center"><strong>{USER_STAT.total}</strong></td>
                        </tr>
                        <!-- END: user_stat_row -->
                        <!-- BEGIN: no_user_stat -->
                        <tr><td colspan="6" class="text-center text-muted">Chưa có dữ liệu</td></tr>
                        <!-- END: no_user_stat -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Stats by Category -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-folder"></i> Theo danh mục
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Danh mục</th>
                            <th class="text-center">Pending</th>
                            <th class="text-center">Doing</th>
                            <th class="text-center">Done</th>
                            <th class="text-center">Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- BEGIN: cat_stat_row -->
                        <tr>
                            <td>
                                <span style="display: inline-block; width: 12px; height: 12px; background-color: {CAT_STAT.color}; border-radius: 2px; margin-right: 5px;"></span>
                                <strong>{CAT_STAT.title}</strong>
                            </td>
                            <td class="text-center">{CAT_STAT.pending}</td>
                            <td class="text-center">{CAT_STAT.doing}</td>
                            <td class="text-center">{CAT_STAT.done}</td>
                            <td class="text-center"><strong>{CAT_STAT.total}</strong></td>
                        </tr>
                        <!-- END: cat_stat_row -->
                        <!-- BEGIN: no_cat_stat -->
                        <tr><td colspan="5" class="text-center text-muted">Chưa có danh mục</td></tr>
                        <!-- END: no_cat_stat -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Overdue Tasks -->
    <div class="col-md-6">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <i class="fa fa-exclamation-triangle"></i> Công việc quá hạn
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tiêu đề</th>
                            <th class="text-center">Hạn chót</th>
                            <th class="text-center">Quá hạn</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- BEGIN: overdue_row -->
                        <tr>
                            <td>{OVERDUE.title}</td>
                            <td class="text-center text-danger">{OVERDUE.due_date_formatted}</td>
                            <td class="text-center"><span class="badge badge-danger">{OVERDUE.days_overdue} ngày</span></td>
                            <td>
                                <a href="{OVERDUE.url_edit}" class="btn btn-xs btn-default">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <!-- END: overdue_row -->
                        <!-- BEGIN: no_overdue -->
                        <tr><td colspan="4" class="text-center text-success"><i class="fa fa-check"></i> Không có công việc quá hạn</td></tr>
                        <!-- END: no_overdue -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Recent Activities -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-history"></i> Hoạt động gần đây
            </div>
            <div class="panel-body" style="max-height: 300px; overflow-y: auto;">
                <ul class="list-unstyled">
                    <!-- BEGIN: activity_row -->
                    <li style="padding: 8px 0; border-bottom: 1px solid #eee;">
                        <strong>{ACTIVITY.user_fullname}</strong>
                        <span class="text-muted">{ACTIVITY.action_text}</span>
                        <em>"{ACTIVITY.work_title}"</em>
                        <br>
                        <small class="text-muted"><i class="fa fa-clock-o"></i> {ACTIVITY.created_at_formatted}</small>
                    </li>
                    <!-- END: activity_row -->
                    <!-- BEGIN: no_activity -->
                    <li class="text-center text-muted">Chưa có hoạt động nào</li>
                    <!-- END: no_activity -->
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- END: main -->
