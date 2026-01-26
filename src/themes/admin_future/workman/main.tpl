<!-- BEGIN: main -->
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr class="text-center">
                <th>ID</th>
                <th>Tiêu đề</th>
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th>Mức độ ưu tiên</th>
                <th>Hạn chót</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <!-- BEGIN: row -->
            <tr>
                <td class="text-center">{ROW.id}</td>
                <td><strong>{ROW.title}</strong></td>
                <td>{ROW.description}</td>
                <td class="text-center">
                    <span class="label {ROW.status_class}">
                        {ROW.status_text}
                    </span>
                </td>
                <td class="text-center">
                    <span class="text-{ROW.priority_class}">
                        {ROW.priority_text}
                    </span>
                </td>
                <td class="text-center">{ROW.due_date}</td>
                <td class="text-center">
                    <a href="{ROW.url_edit}" class="btn btn-xs btn-default" title="Sửa">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="javascript:void(0);"
                       onclick="nv_delete_work({ROW.id});"
                       class="btn btn-xs btn-danger"
                       title="Xóa">
                        <i class="fa fa-trash-o"></i>
                    </a>
                </td>
            </tr>
            <!-- END: row -->
        </tbody>
    </table>
</div>

<script>
</script>
<!-- END: main -->
