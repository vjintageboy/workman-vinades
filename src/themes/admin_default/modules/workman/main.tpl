<!-- BEGIN: main -->
<!-- Stats Overview -->
<div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-info text-center" style="margin-bottom: 0;">
            <div class="panel-body">
                <h3 style="margin: 5px 0;">{STATS.total}</h3>
                <small>Tổng</small>
            </div>
        </div>
    </div>
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-default text-center" style="margin-bottom: 0;">
            <div class="panel-body">
                <h3 style="margin: 5px 0;">{STATS.draft}</h3>
                <small>Bản nháp</small>
            </div>
        </div>
    </div>
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-info text-center" style="margin-bottom: 0;">
            <div class="panel-body">
                <h3 style="margin: 5px 0;">{STATS.pending}</h3>
                <small>Chờ xử lý</small>
            </div>
        </div>
    </div>
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-warning text-center" style="margin-bottom: 0;">
            <div class="panel-body">
                <h3 style="margin: 5px 0;">{STATS.doing}</h3>
                <small>Đang làm</small>
            </div>
        </div>
    </div>
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-primary text-center" style="margin-bottom: 0;">
            <div class="panel-body">
                <h3 style="margin: 5px 0;">{STATS.review}</h3>
                <small>Chờ duyệt</small>
            </div>
        </div>
    </div>
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-success text-center" style="margin-bottom: 0;">
            <div class="panel-body">
                <h3 style="margin: 5px 0;">{STATS.done}</h3>
                <small>Hoàn thành</small>
            </div>
        </div>
    </div>
    <div style="flex: 1; min-width: 100px;">
        <div class="panel panel-danger text-center" style="margin-bottom: 0;">
            <div class="panel-body">
                <h3 style="margin: 5px 0;">{STATS.cancelled}</h3>
                <small>Đã hủy</small>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div style="margin-bottom: 15px;">
    <a href="{URL_ADD}" class="btn btn-primary"><i class="fa fa-plus"></i> Thêm công việc</a>
    <a href="{URL_CATEGORIES}" class="btn btn-default"><i class="fa fa-folder"></i> Danh mục</a>
    <a href="{URL_REPORTS}" class="btn btn-default"><i class="fa fa-bar-chart"></i> Thống kê</a>
</div>

<!-- BEGIN: filter_form -->
<!-- Filters -->
<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-filter"></i> Bộ lọc
    </div>
    <div class="panel-body">
        <form method="get" action="{FORM_ACTION}" class="form-inline">
            <input type="hidden" name="{LANG_VAR}" value="{LANG_DATA}">
            <input type="hidden" name="{NAME_VAR}" value="{MODULE_NAME}">
            <input type="hidden" name="{OP_VAR}" value="main">
            
            <div class="form-group" style="margin-right: 10px;">
                <label>Trạng thái:</label>
                <select name="filter_status" class="form-control input-sm">
                    <option value="">-- Tất cả --</option>
                    <!-- BEGIN: status_option -->
                    <option value="{STATUS_OPTION.key}" {STATUS_OPTION.selected}>{STATUS_OPTION.label}</option>
                    <!-- END: status_option -->
                </select>
            </div>
            
            <div class="form-group" style="margin-right: 10px;">
                <label>Danh mục:</label>
                <select name="filter_category" class="form-control input-sm">
                    <option value="">-- Tất cả --</option>
                    <!-- BEGIN: category_option -->
                    <option value="{CATEGORY_OPTION.id}" {CATEGORY_OPTION.selected}>{CATEGORY_OPTION.title}</option>
                    <!-- END: category_option -->
                </select>
            </div>
            
            <div class="form-group" style="margin-right: 10px;">
                <label>Người thực hiện:</label>
                <select name="filter_assigned" class="form-control input-sm">
                    <option value="">-- Tất cả --</option>
                    <!-- BEGIN: user_option -->
                    <option value="{USER_OPTION.userid}" {USER_OPTION.selected}>{USER_OPTION.fullname}</option>
                    <!-- END: user_option -->
                </select>
            </div>
            
            <div class="form-group" style="margin-right: 10px;">
                <label>Ưu tiên:</label>
                <select name="filter_priority" class="form-control input-sm">
                    <option value="">-- Tất cả --</option>
                    <!-- BEGIN: priority_option -->
                    <option value="{PRIORITY_OPTION.key}" {PRIORITY_OPTION.selected}>{PRIORITY_OPTION.label}</option>
                    <!-- END: priority_option -->
                </select>
            </div>
            
            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Lọc</button>
            <a href="?" class="btn btn-sm btn-default">Reset</a>
        </form>
    </div>
</div>
<!-- END: filter_form -->

<!-- Bulk Actions Form -->
<form method="post" action="{FORM_ACTION}" id="bulk_form">
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6">
                <strong><i class="fa fa-list"></i> Danh sách công việc</strong>
            </div>
            <div class="col-md-6 text-right">
                <select name="bulk_action" class="form-control input-sm" style="width: auto; display: inline-block;">
                    <option value="">-- Thao tác --</option>
                    <option value="delete">Xóa đã chọn</option>
                    <!-- BEGIN: bulk_status_option -->
                    <option value="{BULK_STATUS.key}">Chuyển → {BULK_STATUS.label}</option>
                    <!-- END: bulk_status_option -->
                </select>
                <button type="submit" class="btn btn-sm btn-warning" onclick="return validateBulkAction();">
                    <i class="fa fa-check"></i> Thực hiện
                </button>
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr class="text-center">
                    <th width="30"><input type="checkbox" id="check_all"></th>
                    <th width="50">ID</th>
                    <th>Tiêu đề</th>
                    <th width="100">Danh mục</th>
                    <th width="120">Người thực hiện</th>
                    <th width="80">Trạng thái</th>
                    <th width="80">Ưu tiên</th>
                    <th width="90">Hạn chót</th>
                    <th width="50">File</th>
                    <th width="80">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: row -->
                <tr>
                    <td class="text-center">
                        <input type="checkbox" name="selected_ids[]" value="{ROW.id}" class="row_checkbox">
                    </td>
                    <td class="text-center">{ROW.id}</td>
                    <td>
                        <strong>{ROW.title}</strong>
                        <br><small class="text-muted">{ROW.description}</small>
                    </td>
                    <td class="text-center">
                        <span class="label" style="background-color: {ROW.category_color};">{ROW.category_title}</span>
                    </td>
                    <td class="text-center">
                        <small>{ROW.assigned_name}</small>
                    </td>
                    <td class="text-center">
                        <span class="label label-{ROW.status_class}">{ROW.status_text}</span>
                    </td>
                    <td class="text-center">
                        <span class="text-{ROW.priority_class}">{ROW.priority_text}</span>
                    </td>
                    <td class="text-center {ROW.due_date_class}">{ROW.due_date}</td>
                    <td class="text-center">
                        <!-- BEGIN: attachment -->
                        <a href="{ROW.attachment_url}" target="_blank" title="{ROW.attachment_name}">
                            <i class="fa {ROW.attachment_icon}"></i>
                        </a>
                        <!-- END: attachment -->
                    </td>
                    <td class="text-center">
                        <a href="{ROW.url_edit}" class="btn btn-xs btn-default" title="Sửa">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="javascript:void(0);" onclick="nv_delete_work({ROW.id});" class="btn btn-xs btn-danger" title="Xóa">
                            <i class="fa fa-trash-o"></i>
                        </a>
                    </td>
                </tr>
                <!-- END: row -->
                <!-- BEGIN: no_data -->
                <tr>
                    <td colspan="10" class="text-center" style="padding: 40px;">
                        <i class="fa fa-inbox fa-3x text-muted"></i>
                        <p class="text-muted" style="margin-top: 10px;">Chưa có công việc nào. <a href="{URL_ADD}">Thêm công việc mới</a></p>
                    </td>
                </tr>
                <!-- END: no_data -->
            </tbody>
        </table>
    </div>
</div>
</form>

<!-- BEGIN: generate_page -->
<div class="text-center">
    {GENERATE_PAGE}
</div>
<!-- END: generate_page -->

<script>
// Check all / uncheck all
document.getElementById('check_all').addEventListener('change', function() {
    var checkboxes = document.querySelectorAll('.row_checkbox');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = this.checked;
    }
});

// Validate bulk action
function validateBulkAction() {
    var action = document.querySelector('select[name="bulk_action"]').value;
    var checked = document.querySelectorAll('.row_checkbox:checked').length;
    
    if (!action || action === '') {
        alert('Vui lòng chọn một thao tác!');
        return false;
    }
    
    if (checked === 0) {
        alert('Vui lòng tích chọn ít nhất 1 công việc!');
        return false;
    }
    
    return confirm('Bạn có chắc chắn?');
}

function nv_delete_work(id) {
    if (confirm('Bạn có chắc chắn muốn xóa công việc này không?')) {
        window.location.href = script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=main&delete_id=1&id=' + id;
    }
    return false;
}
</script>
<!-- END: main -->
