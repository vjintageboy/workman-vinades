<div class="panel panel-default">
    <div class="panel-heading">{$TITLE}</div>
    <div class="panel-body">
        {if !empty($ERROR)}
        <div class="alert alert-danger">
            {$ERROR}
        </div>
        {/if}
        
        <form action="{$FORM_ACTION}" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>{$LANG.title} <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{$DATA.title}" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>{$LANG.description}</label>
                <textarea name="description" class="form-control" rows="5">{$DATA.description}</textarea>
            </div>

            <div class="form-group">
                <label>{$LANG.status}</label>
                <select name="status" class="form-control">
                    {foreach from=$STATUS_LIST key=key item=item}
                        <option value="{$key}" {if $DATA.status == $key}selected{/if}>{$item}</option>
                    {/foreach}
                </select>
            </div>

            <div class="form-group">
                <label>{$LANG.priority}</label>
                <select name="priority" class="form-control">
                    {foreach from=$PRIORITY_LIST key=key item=item}
                        <option value="{$key}" {if $DATA.priority == $key}selected{/if}>{$item}</option>
                    {/foreach}
                </select>
            </div>

            <div class="form-group">
                <label>{$LANG.due_date}</label>
                <input type="text" name="due_date" value="{$DATA.due_date}" class="form-control" placeholder="dd/mm/yyyy" autocomplete="off">
                <span class="help-block">Định dạng: 28/01/2026 00:00</span>
            </div>

            <div class="form-group">
                <label>Hình ảnh đính kèm</label>
                <input type="file" name="attachment_image" id="attachment_image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                <span class="help-block">Cho phép: JPG, PNG, GIF, WEBP (Tối đa 2MB)</span>
                
                <div id="image_preview_container" style="margin-top: 10px; {if !$DATA.is_image}display: none;{/if}">
                    <img src="{if $DATA.is_image}{$smarty.const.NV_BASE_SITEURL}{$DATA.attachment}{/if}" id="image_preview" style="max-width: 100%; max-height: 300px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                </div>
            </div>

            <div class="form-group">
                <label>File tài liệu đính kèm (PDF, Word, Excel, ZIP...)</label>
                {if !empty($DATA.attachment) && !$DATA.is_image}
                <div class="alert alert-info">
                    File hiện tại: <a href="{$smarty.const.NV_BASE_SITEURL}{$DATA.attachment}" target="_blank">
                        <i class="fa fa-file"></i> {$DATA.attachment_name}
                    </a>
                </div>
                {/if}
                <input type="file" name="attachment" class="form-control">
                <span class="help-block">Cho phép: PDF, DOC, DOCX, XLS, XLSX, ZIP (Tối đa 2MB)</span>
            </div>

            <div class="text-center">
                <button type="submit" name="submit" value="1" class="btn btn-primary">
                    <i class="fa fa-save"></i> {$LANG.save}
                </button>
                <a href="{$URL_BACK}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> {$GLANG.back}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Preview image khi chọn file
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