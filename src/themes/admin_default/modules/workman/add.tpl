<!-- BEGIN: main -->
<div class="panel panel-default">
    <div class="panel-heading">{$TITLE}</div>
    <div class="panel-body">
        <form action="{$FORM_ACTION}" method="post">
            <div class="form-group">
                <label>{$LANG.title}</label>
                <input type="text" name="title" value="{$DATA.title}" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>{$LANG.description}</label>
                <textarea name="description" class="form-control">{$DATA.description}</textarea>
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
                <input type="text" name="due_date" value="{$DATA.due_date}" class="form-control" id="due_date_picker">
            </div>

            <div class="text-center">
                <button type="submit" name="submit" value="1" class="btn btn-primary">{$LANG.save}</button>
            </div>
        </form>
    </div>
</div>
<!-- END: main -->