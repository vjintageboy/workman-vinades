<!-- BEGIN: main -->
<div class="workman-list">
    <h2 class="page-header">
        <i class="fa fa-tasks"></i> Công việc của tôi
        <a href="{URL_DASHBOARD}" class="btn btn-default btn-sm pull-right">
            <i class="fa fa-dashboard"></i> Dashboard
        </a>
    </h2>
    
    <!-- Filter -->
    <div class="panel panel-default">
        <div class="panel-body">
            <form method="get" action="{FORM_ACTION}" class="form-inline">
                <div class="form-group">
                    <label>Trạng thái:</label>
                    <select name="status" class="form-control input-sm">
                        <option value="">-- Tất cả --</option>
                        <!-- BEGIN: status_option -->
                        <option value="{STATUS_OPTION.key}" {STATUS_OPTION.selected}>{STATUS_OPTION.label}</option>
                        <!-- END: status_option -->
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Lọc</button>
            </form>
        </div>
    </div>
    
    <!-- Task List -->
    <div class="panel panel-default">
        <div class="panel-heading">
            Tổng: <strong>{TOTAL}</strong> công việc
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th width="100">Danh mục</th>
                        <th width="80">Trạng thái</th>
                        <th width="80">Ưu tiên</th>
                        <th width="100">Hạn chót</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: task -->
                    <tr>
                        <td>
                            <a href="{TASK.url_detail}">
                                <strong>{TASK.title}</strong>
                            </a>
                            <!-- BEGIN: overdue -->
                            <span class="label label-danger">Quá hạn!</span>
                            <!-- END: overdue -->
                        </td>
                        <td>
                            <span class="label" style="background-color: {TASK.category_color};">{TASK.category_title}</span>
                        </td>
                        <td>
                            <span class="label label-{TASK.status_class}">{TASK.status_text}</span>
                        </td>
                        <td>
                            <span class="text-{TASK.priority_class}">{TASK.priority_text}</span>
                        </td>
                        <td>{TASK.due_date_formatted}</td>
                    </tr>
                    <!-- END: task -->
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- BEGIN: no_tasks -->
    <div class="alert alert-info text-center">
        Không có công việc nào.
    </div>
    <!-- END: no_tasks -->
    
    <!-- BEGIN: pagination -->
    <div class="text-center">
        {GENERATE_PAGE}
    </div>
    <!-- END: pagination -->
</div>
<!-- END: main -->
