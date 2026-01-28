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
                <input type="text" name="due_date" value="{$DATA.due_date}" class="form-control" id="due_date_picker" autocomplete="off">
            </div>

            <div class="form-group">
                <label>Hình ảnh đính kèm</label>
                {if $DATA.is_image}
                <div style="margin-bottom: 10px;">
                    <img src="{$smarty.const.NV_BASE_SITEURL}{$DATA.attachment}" id="image_preview" style="max-width: 300px; max-height: 200px; border: 1px solid #ddd; padding: 5px; display: block;">
                </div>
                {else}
                <div style="margin-bottom: 10px; display: none;" id="image_preview_container">
                    <img src="" id="image_preview" style="max-width: 300px; max-height: 200px; border: 1px solid #ddd; padding: 5px; display: block;">
                </div>
                {/if}
                <input type="file" name="attachment_image" id="attachment_image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                <span class="help-block">Cho phép: JPG, PNG, GIF, WEBP (Tối đa 2MB)</span>
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
$(document).ready(function() {
    // Datepicker
    $('#due_date_picker').datepicker({
        format: 'dd/mm/yyyy hh:ii',
        language: '{$smarty.const.NV_LANG_DATA}',
        autoclose: true,
        todayHighlight: true
    });
    
    // Preview image khi chọn file
    $('#attachment_image').change(function(e) {
        var file = e.target.files[0];
        if (file && file.type.match('image.*')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image_preview').attr('src', e.target.result);
                $('#image_preview_container').show();
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>