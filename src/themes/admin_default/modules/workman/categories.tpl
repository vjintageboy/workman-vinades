<!-- BEGIN: main -->
<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-folder"></i> Quản lý danh mục
        <a href="{URL_BACK}" class="btn btn-xs btn-default pull-right">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>
    </div>
    <div class="panel-body">
        <!-- BEGIN: error -->
        <div class="alert alert-danger">{ERROR}</div>
        <!-- END: error -->
        
        <div class="row">
            <!-- Form thêm/sửa -->
            <div class="col-md-4">
                <!-- BEGIN: form_add -->
                <div class="panel panel-primary">
                    <div class="panel-heading">Thêm danh mục mới</div>
                <!-- END: form_add -->
                <!-- BEGIN: form_edit -->
                <div class="panel panel-warning">
                    <div class="panel-heading">Sửa danh mục</div>
                <!-- END: form_edit -->
                    <div class="panel-body">
                        <form method="post" action="{FORM_ACTION}">
                            <input type="hidden" name="id" value="{FORM_DATA.id}">
                            
                            <div class="form-group">
                                <label>Tên danh mục <span class="text-danger">*</span></label>
                                <input type="text" name="title" value="{FORM_DATA.title}" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Mô tả</label>
                                <input type="text" name="description" value="{FORM_DATA.description}" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label>Màu hiển thị</label>
                                <input type="color" name="color" value="{FORM_DATA.color}" class="form-control" style="height: 40px; padding: 5px;">
                            </div>
                            
                            <div class="form-group">
                                <label>Thứ tự</label>
                                <input type="number" name="weight" value="{FORM_DATA.weight}" class="form-control" min="0">
                            </div>
                            
                            <div class="form-group">
                                <label>Trạng thái</label>
                                <select name="status" class="form-control">
                                    <!-- BEGIN: status_active -->
                                    <option value="1" selected>Hoạt động</option>
                                    <option value="0">Tạm ẩn</option>
                                    <!-- END: status_active -->
                                    <!-- BEGIN: status_inactive -->
                                    <option value="1">Hoạt động</option>
                                    <option value="0" selected>Tạm ẩn</option>
                                    <!-- END: status_inactive -->
                                </select>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" name="submit" value="1" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Lưu
                                </button>
                                <!-- BEGIN: btn_cancel -->
                                <a href="{FORM_ACTION}" class="btn btn-default">Hủy</a>
                                <!-- END: btn_cancel -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Danh sách -->
            <div class="col-md-8">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Tên danh mục</th>
                            <th width="60">Màu</th>
                            <th width="50">STT</th>
                            <th width="80">Số task</th>
                            <th width="80">Trạng thái</th>
                            <th width="100">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- BEGIN: category_row -->
                        <tr>
                            <td class="text-center">{CAT.id}</td>
                            <td>
                                <strong>{CAT.title}</strong>
                                <br><small class="text-muted">{CAT.description}</small>
                            </td>
                            <td class="text-center">
                                <span style="display: inline-block; width: 24px; height: 24px; background-color: {CAT.color}; border-radius: 4px;"></span>
                            </td>
                            <td class="text-center">{CAT.weight}</td>
                            <td class="text-center">
                                <span class="badge">{CAT.task_count}</span>
                            </td>
                            <td class="text-center">
                                <span class="label label-{CAT.status_class}">{CAT.status_text}</span>
                            </td>
                            <td class="text-center">
                                <a href="{CAT.url_edit}" class="btn btn-xs btn-default" title="Sửa">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="javascript:void(0);" onclick="if(confirm('Xóa danh mục này?')) location.href='{CAT.url_delete}';" class="btn btn-xs btn-danger" title="Xóa">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <!-- END: category_row -->
                        <!-- BEGIN: no_data -->
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                <i class="fa fa-folder-open fa-2x"></i>
                                <p>Chưa có danh mục nào. Hãy thêm danh mục mới.</p>
                            </td>
                        </tr>
                        <!-- END: no_data -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- END: main -->
