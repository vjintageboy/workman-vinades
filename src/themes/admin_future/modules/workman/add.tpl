<div class="card border-primary border-3 border-bottom-0 border-start-0 border-end-0 mb-3">
    <div class="card-header d-flex justify-content-between align-items-center py-2">
        <span class="fw-medium fs-5"><i class="fa-solid fa-plus"></i> {$TITLE}</span>
        <a href="{$URL_BACK}" class="btn btn-sm btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> {$LANG->getGlobal('back')}
        </a>
    </div>
    <div class="card-body">
        {if !empty($ERROR)}
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {$ERROR}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        {/if}
        
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row g-3">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('title')} <span class="text-danger">*</span></label>
                        <input type="text" name="title" value="{$DATA.title}" class="form-control" required>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('description')}</label>
                        <textarea name="description" class="form-control" rows="8">{$DATA.description}</textarea>
                    </div>
                    
                    <!-- Attachments -->
                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-paperclip"></i> {$LANG->getModule('attachment')}
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fa-solid fa-image"></i> {$LANG->getModule('attachment_image')}</label>
                                    <input type="file" name="attachment_image" id="attachment_image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                                    <div class="form-text">JPG, PNG, GIF, WEBP (Max 2MB)</div>
                                    
                                    <div id="image_preview_container" class="mt-2" style="{if !$DATA.is_image}display: none;{/if}">
                                        <img src="{if $DATA.is_image}{$smarty.const.NV_BASE_SITEURL}{$DATA.attachment}{/if}" id="image_preview" class="img-fluid rounded border" style="max-height: 200px;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="fa-solid fa-file"></i> {$LANG->getModule('attachment')}</label>
                                    {if !empty($DATA.attachment) && !$DATA.is_image}
                                    <div class="alert alert-info py-2 mb-2">
                                        <i class="fa-solid fa-file"></i> 
                                        <a href="{$smarty.const.NV_BASE_SITEURL}{$DATA.attachment}" target="_blank">{$DATA.attachment_name}</a>
                                    </div>
                                    {/if}
                                    <input type="file" name="attachment" class="form-control">
                                    <div class="form-text">PDF, DOC, DOCX, XLS, XLSX, ZIP (Max 2MB)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Category -->
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('category')}</label>
                        <select name="category_id" class="form-select">
                            <option value="0">{$LANG->getModule('select_category')}</option>
                            {foreach from=$CATEGORIES key=cat_id item=cat}
                            <option value="{$cat.id}" {if $DATA.category_id == $cat.id}selected{/if} style="border-left: 4px solid {$cat.color};">
                                {$cat.title}
                            </option>
                            {/foreach}
                        </select>
                    </div>
                    
                    <!-- Assigned To -->
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('assigned_to')}</label>
                        <select name="assigned_to" class="form-select">
                            <option value="0">{$LANG->getModule('select_user')}</option>
                            {foreach from=$USERS key=uid item=user}
                            <option value="{$user.userid}" {if $DATA.assigned_to == $user.userid}selected{/if}>
                                {$user.fullname} ({$user.username})
                            </option>
                            {/foreach}
                        </select>
                    </div>
                    
                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('status')}</label>
                        <select name="status" class="form-select">
                            {foreach from=$STATUS_LIST key=key item=item}
                            <option value="{$key}" {if $DATA.status == $key}selected{/if}>{$item}</option>
                            {/foreach}
                        </select>
                    </div>

                    <!-- Priority -->
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('priority')}</label>
                        <select name="priority" class="form-select">
                            {foreach from=$PRIORITY_LIST key=key item=item}
                            <option value="{$key}" {if $DATA.priority == $key}selected{/if}>{$item}</option>
                            {/foreach}
                        </select>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('due_date')}</label>
                        <input type="text" name="due_date" value="{$DATA.due_date}" class="form-control" placeholder="dd/mm/yyyy HH:ii" autocomplete="off">
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-center gap-2">
                <button type="submit" name="submit" value="1" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> {$LANG->getModule('save')}
                </button>
                <a href="{$URL_BACK}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> {$LANG->getGlobal('back')}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var imageInput = document.getElementById('attachment_image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file && file.type.indexOf('image') !== -1) {
                var reader = new FileReader();
                reader.onload = function(evt) {
                    document.getElementById('image_preview').src = evt.target.result;
                    document.getElementById('image_preview_container').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>