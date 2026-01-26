<!-- BEGIN: main -->
<div class="page-header">
    <h1>Workman</h1>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <a href="{URL_ADD}" class="btn btn-success btn-sm">
                <i class="fa fa-plus"></i> Thêm mới
            </a>
        </div>
        <h3 class="panel-title">Danh sách công việc</h3>
        <div class="clearfix"></div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                    <th>Ưu tiên</th>
                    <th>Hạn chót</th>
                    <th width="100">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: row -->
                <tr>
                    <td>{ROW.id}</td>
                    <td><strong>{ROW.title}</strong></td>
                    <td>{ROW.description}</td>
                    <td>
                        <span class="label label-{ROW.status_class}">{ROW.status_text}</span>
                    </td>
                    <td>
                        <span class="label label-{ROW.priority_class}">{ROW.priority_text}</span>
                    </td>
                    <td>{ROW.due_date}</td>
                    <td>
                        <a href="{ROW.url_edit}" class="btn btn-sm btn-primary" title="Sửa">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="{ROW.url_delete}" onclick="return confirm(nv_is_del_confirm[0]);" class="btn btn-sm btn-danger" title="Xóa">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <!-- END: row -->
            </tbody>
        </table>
    </div>
</div>
<!-- END: main -->