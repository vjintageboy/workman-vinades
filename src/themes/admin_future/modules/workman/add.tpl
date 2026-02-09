<div class="card border-0 shadow-sm mb-4">
    <!-- Header with gradient based on mode -->
    <div class="card-header py-3" style="background: linear-gradient(135deg, {if $IS_EDIT}#f093fb 0%, #f5576c 100%{else}#4facfe 0%, #00f2fe 100%{/if}); border: none;">
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold fs-5 text-white">
                {if $IS_EDIT}
                <i class="fa-solid fa-edit"></i> Chỉnh sửa công việc
                {else}
                <i class="fa-solid fa-plus-circle"></i> Thêm công việc mới
                {/if}
            </span>
            <a href="{$URL_BACK}" class="btn btn-sm btn-light">
                <i class="fa-solid fa-arrow-left"></i> {$LANG->getGlobal('back')}
            </a>
        </div>
    </div>
    
    <div class="card-body p-4">
        {if !empty($ERROR)}
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-exclamation-circle"></i> {$ERROR}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        {/if}
        
        <form action="" method="post" enctype="multipart/form-data" id="workForm">
            <div class="row g-4">
                <!-- Main Content Column -->
                <div class="col-lg-8">
                    <!-- Step 1: Basic Info -->
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <h6 class="text-primary mb-3">
                                <span class="badge bg-primary rounded-circle me-2">1</span>
                                Thông tin cơ bản
                            </h6>
                            
                            <!-- Title -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    {$LANG->getModule('title')} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="title" id="title_input" value="{$WORKMAN_ROW.title|escape:'html'}" 
                                       class="form-control form-control-lg" required maxlength="255"
                                       placeholder="Nhập tiêu đề công việc...">
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Tiêu đề ngắn gọn, dễ hiểu</small>
                                    <small class="text-muted"><span id="title_count">0</span>/255</small>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="mb-0">
                                <label class="form-label fw-semibold">{$LANG->getModule('description')}</label>
                                <textarea name="description" id="desc_input" class="form-control" rows="6" 
                                          maxlength="5000" placeholder="Mô tả chi tiết công việc...">{$WORKMAN_ROW.description|escape:'html'}</textarea>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Mô tả đầy đủ yêu cầu, tiêu chí hoàn thành</small>
                                    <small class="text-muted"><span id="desc_count">0</span>/5000</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Attachments -->
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="text-primary mb-3">
                                <span class="badge bg-primary rounded-circle me-2">2</span>
                                Tệp đính kèm
                            </h6>
                            
                            <!-- Unified Drop Zone -->
                            <div id="drop_zone" class="border-2 border-dashed rounded-3 p-4 text-center bg-white"
                                 style="border-color: #dee2e6; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fa-solid fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2 fw-semibold">Kéo thả file vào đây hoặc nhấn để chọn</p>
                                <p class="text-muted small mb-3">Hỗ trợ: JPG, PNG, GIF, WEBP, PDF, DOC, DOCX, XLS, XLSX, ZIP (Max 2MB)</p>
                                
                                <div class="row g-2 justify-content-center">
                                    <div class="col-auto">
                                        <label class="btn btn-outline-primary btn-sm">
                                            <i class="fa-solid fa-image"></i> Chọn ảnh
                                            <input type="file" name="attachment_image" id="attachment_image" class="d-none" 
                                                   accept="image/jpeg,image/png,image/gif,image/webp">
                                        </label>
                                    </div>
                                    <div class="col-auto">
                                        <label class="btn btn-outline-secondary btn-sm">
                                            <i class="fa-solid fa-file"></i> Chọn tài liệu
                                            <input type="file" name="attachment" id="attachment_file" class="d-none"
                                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.zip">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Preview Area -->
                            <div id="attachment_preview" class="mt-3" style="{if empty($WORKMAN_ROW.attachment)}display: none;{/if}">
                                {if $WORKMAN_ROW.is_image}
                                <div class="d-flex align-items-center p-2 bg-white rounded border">
                                    <img src="{$smarty.const.NV_BASE_SITEURL}{$WORKMAN_ROW.attachment}" 
                                         class="rounded" style="max-height: 80px; max-width: 120px;" id="preview_img">
                                    <div class="ms-3 flex-grow-1">
                                        <strong id="preview_name">{$WORKMAN_ROW.attachment_name}</strong>
                                        <br><small class="text-muted">Đã tải lên</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearPreview();">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                                {elseif !empty($WORKMAN_ROW.attachment)}
                                <div class="d-flex align-items-center p-2 bg-white rounded border">
                                    <i class="fa-solid fa-file-alt fa-2x text-secondary"></i>
                                    <div class="ms-3 flex-grow-1">
                                        <a href="{$smarty.const.NV_BASE_SITEURL}{$WORKMAN_ROW.attachment}" target="_blank">
                                            <strong id="preview_name">{$WORKMAN_ROW.attachment_name}</strong>
                                        </a>
                                        <br><small class="text-muted">Đã tải lên</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearPreview();">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar Column -->
                <div class="col-lg-4">
                    <!-- Card: Classification -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0"><i class="fa-solid fa-folder text-info"></i> Phân loại</h6>
                        </div>
                        <div class="card-body pt-2">
                            <!-- Category -->
                            <div class="mb-3">
                                <label class="form-label small text-muted">{$LANG->getModule('category')}</label>
                                <select name="category_id" class="form-select">
                                    <option value="0">{$LANG->getModule('select_category')}</option>
                                    {foreach from=$CATEGORIES key=cat_id item=cat}
                                    <option value="{$cat.id}" {if $WORKMAN_ROW.category_id == $cat.id}selected{/if} 
                                            data-color="{$cat.color}">
                                        ● {$cat.title}
                                    </option>
                                    {/foreach}
                                </select>
                            </div>
                            
                            <!-- Assigned To -->
                            <div class="mb-0">
                                <label class="form-label small text-muted">{$LANG->getModule('assigned_to')}</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="0">{$LANG->getModule('select_user')}</option>
                                    {foreach from=$USERS key=uid item=user}
                                    <option value="{$user.userid}" {if $WORKMAN_ROW.assigned_to == $user.userid}selected{/if}>
                                        👤 {$user.fullname}
                                    </option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card: Status & Priority -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0"><i class="fa-solid fa-sliders-h text-warning"></i> Trạng thái & Độ ưu tiên</h6>
                        </div>
                        <div class="card-body pt-2">
                            <!-- Status as color buttons -->
                            <div class="mb-3">
                                <label class="form-label small text-muted">{$LANG->getModule('status')}</label>
                                <div class="d-flex flex-wrap gap-2">
                                    {foreach from=$STATUS_LIST key=key item=item}
                                    <div>
                                        <input type="radio" class="btn-check" name="status" id="status_{$key}" 
                                               value="{$key}" {if $WORKMAN_ROW.status == $key}checked{/if}>
                                        <label class="btn btn-sm btn-outline-{$LANG->getModule("status_class_{$key}")}" for="status_{$key}">
                                            {$item}
                                        </label>
                                    </div>
                                    {/foreach}
                                </div>
                            </div>
                            
                            <!-- Priority as color badges -->
                            <div class="mb-0">
                                <label class="form-label small text-muted">{$LANG->getModule('priority')}</label>
                                <div class="d-flex flex-wrap gap-2">
                                    {foreach from=$PRIORITY_LIST key=key item=item}
                                    <div>
                                        <input type="radio" class="btn-check" name="priority" id="priority_{$key}" 
                                               value="{$key}" {if $WORKMAN_ROW.priority == $key}checked{/if}>
                                        <label class="btn btn-sm btn-outline-{$LANG->getModule("priority_class_{$key}")}" for="priority_{$key}">
                                            {if $key == 'urgent'}<i class="fa-solid fa-fire"></i>{/if}
                                            {if $key == 'high'}<i class="fa-solid fa-arrow-up"></i>{/if}
                                            {if $key == 'normal'}<i class="fa-solid fa-minus"></i>{/if}
                                            {if $key == 'low'}<i class="fa-solid fa-arrow-down"></i>{/if}
                                            {$item}
                                        </label>
                                    </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card: Due Date -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0"><i class="fa-solid fa-calendar-alt text-danger"></i> Thời hạn</h6>
                        </div>
                        <div class="card-body pt-2">
                            <input type="datetime-local" name="due_date" id="due_date" value="{$WORKMAN_ROW.due_date}" 
                                   class="form-control mb-2">
                            
                            <!-- Quick date buttons -->
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-date" data-days="0">
                                    Hôm nay
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-date" data-days="1">
                                    Ngày mai
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-date" data-days="7">
                                    1 tuần
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-date" data-days="30">
                                    1 tháng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="border-top pt-4 mt-4">
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <button type="submit" name="submit" value="1" class="btn btn-primary btn-lg px-4">
                        <i class="fa-solid fa-save"></i> {$LANG->getModule('save')}
                    </button>
                    {if !$IS_EDIT}
                    <button type="submit" name="submit" value="2" class="btn btn-success btn-lg px-4">
                        <i class="fa-solid fa-plus"></i> Lưu & Thêm mới
                    </button>
                    {/if}
                    <a href="{$URL_BACK}" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="fa-solid fa-times"></i> Hủy
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
#drop_zone:hover, #drop_zone.dragover {
    border-color: #0d6efd !important;
    background-color: #f0f7ff !important;
}
.btn-check:checked + .btn {
    color: #fff !important;
}
.quick-date:hover {
    background-color: #0d6efd;
    color: #fff;
    border-color: #0d6efd;
}
</style>

<script>
{literal}
document.addEventListener('DOMContentLoaded', function() {
    // Character counters
    const titleInput = document.getElementById('title_input');
    const descInput = document.getElementById('desc_input');
    const titleCount = document.getElementById('title_count');
    const descCount = document.getElementById('desc_count');
    
    function updateCount(input, counter) {
        counter.textContent = input.value.length;
    }
    
    if (titleInput && titleCount) {
        updateCount(titleInput, titleCount);
        titleInput.addEventListener('input', () => updateCount(titleInput, titleCount));
    }
    if (descInput && descCount) {
        updateCount(descInput, descCount);
        descInput.addEventListener('input', () => updateCount(descInput, descCount));
    }
    
    // Quick date buttons
    document.querySelectorAll('.quick-date').forEach(btn => {
        btn.addEventListener('click', function() {
            const days = parseInt(this.dataset.days);
            const date = new Date();
            date.setDate(date.getDate() + days);
            date.setHours(17, 0, 0, 0); // Default to 5 PM
            
            const formatted = date.toISOString().slice(0, 16);
            document.getElementById('due_date').value = formatted;
        });
    });
    
    // Image preview
    document.getElementById('attachment_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type.indexOf('image') !== -1) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const preview = document.getElementById('attachment_preview');
                preview.innerHTML = `
                    <div class="d-flex align-items-center p-2 bg-white rounded border">
                        <img src="${evt.target.result}" class="rounded" style="max-height: 80px; max-width: 120px;">
                        <div class="ms-3 flex-grow-1">
                            <strong>${file.name}</strong>
                            <br><small class="text-success">Đã chọn - chờ upload</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearPreview();">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                `;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
    
    // File preview
    document.getElementById('attachment_file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const preview = document.getElementById('attachment_preview');
            preview.innerHTML = `
                <div class="d-flex align-items-center p-2 bg-white rounded border">
                    <i class="fa-solid fa-file-alt fa-2x text-secondary"></i>
                    <div class="ms-3 flex-grow-1">
                        <strong>${file.name}</strong>
                        <br><small class="text-success">Đã chọn - chờ upload</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearPreview();">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            `;
            preview.style.display = 'block';
        }
    });
    
    // Drop zone visual feedback
    const dropZone = document.getElementById('drop_zone');
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    dropZone.addEventListener('dragleave', function(e) {
        this.classList.remove('dragover');
    });
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        // Note: actual file handling would need more code for drag-drop
    });
});

function clearPreview() {
    document.getElementById('attachment_preview').style.display = 'none';
    document.getElementById('attachment_image').value = '';
    document.getElementById('attachment_file').value = '';
}
{/literal}

// Load CKEditor CSS and JS if not already loaded, then init editors
(function() {
    function initDescEditor() {
        var descTextarea = document.getElementById('desc_input');
        if (!descTextarea) return;

        ClassicEditor
        .create(descTextarea, {
            language: '{$smarty.const.NV_LANG_INTERFACE}',
            toolbar: {
                items: [
                    'undo', 'redo', '|',
                    'heading', '|',
                    'bold', 'italic', 'underline', '|',
                    'link', 'bulletedList', 'numberedList', '|',
                    'blockQuote', 'insertTable'
                ]
            },
            removePlugins: ['NVBox']
        })
        .then(editor => {
            var form = descTextarea.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    descTextarea.value = editor.getData();
                });
            }
        })
        .catch(error => console.error(error));
    }

    if (typeof ClassicEditor !== 'undefined') {
        initDescEditor();
    } else {
        var ckCSS = document.createElement('link');
        ckCSS.rel = 'stylesheet';
        ckCSS.href = '{$smarty.const.NV_STATIC_URL}{$smarty.const.NV_EDITORSDIR}/ckeditor5-classic/ckeditor.css?t={$smarty.const.NV_CURRENTTIME}';
        document.head.appendChild(ckCSS);

        var ckJS = document.createElement('script');
        ckJS.src = '{$smarty.const.NV_STATIC_URL}{$smarty.const.NV_EDITORSDIR}/ckeditor5-classic/ckeditor.js?t={$smarty.const.NV_CURRENTTIME}';
        ckJS.onload = function() {
            var ckLang = document.createElement('script');
            ckLang.src = '{$smarty.const.NV_STATIC_URL}{$smarty.const.NV_EDITORSDIR}/ckeditor5-classic/language/{$smarty.const.NV_LANG_INTERFACE}.js?t={$smarty.const.NV_CURRENTTIME}';
            ckLang.onload = function() {
                initDescEditor();
            };
            document.body.appendChild(ckLang);
        };
        document.body.appendChild(ckJS);
    }
})();
</script>

</script>