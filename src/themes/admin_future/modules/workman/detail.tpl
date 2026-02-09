<div class="card border-primary border-3 border-bottom-0 border-start-0 border-end-0 mb-3">
    <div class="card-body">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="mb-1">{$TASK.title}</h4>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-{$TASK.status_class}">{$TASK.status_text}</span>
                    <span class="badge bg-{$TASK.priority_class}">{$TASK.priority_text}</span>
                    {if $TASK.category_title}
                    <span class="badge" style="background-color: {$TASK.category_color};">{$TASK.category_title}</span>
                    {/if}
                    {if $TASK.is_overdue}
                    <span class="badge bg-danger"><i class="fa-solid fa-exclamation-triangle"></i>
                        {$LANG->getModule('overdue')}</span>
                    {/if}
                </div>
            </div>
            <div>
                <a href="{$URL_EDIT}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-edit"></i> {$LANG->getGlobal('edit')}
                </a>
                <a href="{$URL_BACK}" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> {$LANG->getGlobal('back')}
                </a>
            </div>
        </div>

        <div class="row g-3">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Task Info -->
                <div class="card mb-3">
                    <div class="card-header">
                        <strong><i class="fa-solid fa-info-circle"></i> {$LANG->getModule('task_info')}</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <p class="mb-2"><strong>{$LANG->getModule('status')}:</strong>
                                    <span class="badge bg-{$TASK.status_class}">{$TASK.status_text}</span>
                                </p>
                                <p class="mb-2"><strong>{$LANG->getModule('priority')}:</strong>
                                    <span class="badge bg-{$TASK.priority_class}">{$TASK.priority_text}</span>
                                </p>
                                <p class="mb-2"><strong>{$LANG->getModule('category')}:</strong>
                                    {if $TASK.category_title}
                                    <span class="badge"
                                        style="background-color: {$TASK.category_color};">{$TASK.category_title}</span>
                                    {else}
                                    <span class="text-muted">-</span>
                                    {/if}
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-2"><strong>{$LANG->getModule('created_by')}:</strong> {$TASK.creator_name}
                                </p>
                                <p class="mb-2"><strong>{$LANG->getModule('assigned_to')}:</strong>
                                    {$TASK.assigned_name}</p>
                                <p class="mb-2"><strong>{$LANG->getModule('due_date')}:</strong>
                                    <span class="{if $TASK.is_overdue}text-danger fw-bold{/if}">
                                        {if $TASK.due_date_formatted}{$TASK.due_date_formatted}{else}-{/if}
                                    </span>
                                </p>
                                <p class="mb-2"><strong>{$LANG->getModule('created_at')}:</strong>
                                    {$TASK.created_at_formatted}</p>
                                {if $TASK.completed_at_formatted}
                                <p class="mb-2"><strong>{$LANG->getModule('completed_at')}:</strong>
                                    <span class="text-success">{$TASK.completed_at_formatted}</span>
                                </p>
                                {/if}
                            </div>
                        </div>

                        <hr>

                        <div class="task-description">
                            <h6 class="mb-2"><i class="fa-solid fa-align-left"></i> {$LANG->getModule('description')}:
                            </h6>
                            <div class="bg-light p-3 rounded">
                                {if $TASK.description}{$TASK.description nofilter}{else}<em
                                    class="text-muted">{$LANG->getModule('no_description')}</em>{/if}
                            </div>
                        </div>

                        {if !empty($TASK.attachment)}
                        <hr>
                        <div class="task-attachment">
                            <h6 class="mb-2"><i class="fa-solid fa-paperclip"></i> {$LANG->getModule('attachment')}:
                            </h6>
                            {if $TASK.is_image}
                            <div class="text-center">
                                <a href="{$TASK.attachment_url}" target="_blank">
                                    <img src="{$TASK.attachment_url}" alt="{$TASK.attachment_name}"
                                        class="img-fluid rounded" style="max-height: 400px;">
                                </a>
                            </div>
                            {else}
                            <a href="{$TASK.attachment_url}" target="_blank" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-download"></i> {$TASK.attachment_name}
                            </a>
                            {/if}
                        </div>
                        {/if}
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <strong><i class="fa-solid fa-comments"></i> {$LANG->getModule('comments')}
                            ({$TASK.comment_count})</strong>
                    </div>
                    <div class="card-body">
                        {if empty($COMMENTS)}
                        <div class="text-center text-muted py-4">
                            <i class="fa-regular fa-comments fa-3x opacity-25"></i>
                            <p class="mt-2">{$LANG->getModule('no_comments')}</p>
                        </div>
                        {else}
                        {foreach $COMMENTS as $COMMENT}
                        <div class="d-flex mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong>{$COMMENT.user_fullname}</strong>
                                    {if $COMMENT.is_admin}<span class="badge bg-primary ms-1">Admin</span>{/if}
                                    <small class="text-muted">{$COMMENT.created_at_formatted}</small>
                                </div>
                                <div>{$COMMENT.content nofilter}</div>
                                {if !empty($COMMENT.attachment)}
                                <div class="mt-2">
                                    <a href="{$COMMENT.attachment_url}" target="_blank"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-paperclip"></i> {$COMMENT.attachment_name}
                                    </a>
                                </div>
                                {/if}
                            </div>
                        </div>
                        {/foreach}
                        {/if}
                    </div>
                </div>

                <!-- Comment Form -->
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <strong><i class="fa-solid fa-reply"></i> {$LANG->getModule('add_comment')}</strong>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{$URL_COMMENT}" enctype="multipart/form-data">
                            <input type="hidden" name="work_id" value="{$TASK_ID}">
                            <div class="mb-3">
                                <textarea name="content" class="form-control" rows="4"
                                    placeholder="{$LANG->getModule('comment_placeholder')}" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><i class="fa-solid fa-paperclip"></i>
                                    {$LANG->getModule('attachment_optional')}</label>
                                <input type="file" name="attachment" class="form-control">
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="submit_comment" class="btn btn-info">
                                    <i class="fa-solid fa-paper-plane"></i> {$LANG->getModule('send_comment')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Activity Log -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <strong><i class="fa-solid fa-history"></i> {$LANG->getModule('activity_log')}</strong>
                    </div>
                    <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            {foreach $LOGS as $LOG}
                            <li class="list-group-item">
                                <strong class="d-block">{$LOG.user_fullname}</strong>
                                <span class="text-info">{$LOG.action_text}</span>
                                {if !empty($LOG.old_value) || !empty($LOG.new_value)}
                                <div class="small mt-1">
                                    {if !empty($LOG.old_value)}<span class="text-danger">{$LOG.old_value}</span> → {/if}
                                    <span class="text-success">{$LOG.new_value}</span>
                                </div>
                                {/if}
                                <small class="text-muted d-block mt-1">
                                    <i class="fa-solid fa-clock"></i> {$LOG.created_at_formatted}
                                </small>
                            </li>
                            {/foreach}
                        </ul>
                    </div>
                </div>

                <!-- Quick Status Change -->
                <div class="card mt-3">
                    <div class="card-header">
                        <strong><i class="fa-solid fa-exchange-alt"></i>
                            {$LANG->getModule('quick_status_change')}</strong>
                    </div>
                    <div class="card-body">
                        <select id="quick_status" class="form-select">
                            {foreach $STATUS_LIST as $key => $label}
                            <option value="{$key}" {if $TASK.status==$key}selected{/if}>{$label}</option>
                            {/foreach}
                        </select>
                        <button type="button" class="btn btn-primary w-100 mt-2" onclick="updateStatus();">
                            <i class="fa-solid fa-save"></i> {$LANG->getModule('update_status')}
                        </button>
                    </div>
                </div>

                <!-- Quick Priority Change -->
                <div class="card mt-3">
                    <div class="card-header">
                        <strong><i class="fa-solid fa-flag"></i>
                            {$LANG->getModule('quick_priority_change')}</strong>
                    </div>
                    <div class="card-body">
                        <select id="quick_priority" class="form-select">
                            {foreach $PRIORITY_LIST as $key => $label}
                            <option value="{$key}" {if $TASK.priority==$key}selected{/if}>{$label}</option>
                            {/foreach}
                        </select>
                        <button type="button" class="btn btn-warning w-100 mt-2" onclick="updatePriority();">
                            <i class="fa-solid fa-save"></i> {$LANG->getModule('update_priority')}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateStatus() {
        var newStatus = document.getElementById('quick_status').value;
        if (!confirm('{$LANG->getModule("confirm_update_status")}')) return;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&{$smarty.const.NV_OP_VARIABLE}=ajax', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.error === 0) {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert('Error: ' + res.message);
                    }
                } catch (e) {
                    alert('An error occurred');
                }
            }
        };
        xhr.send('action=update_status&id={$TASK_ID}&status=' + newStatus);
    }

    function updatePriority() {
        var newPriority = document.getElementById('quick_priority').value;
        if (!confirm('{$LANG->getModule("confirm_update_priority")}')) return;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&{$smarty.const.NV_OP_VARIABLE}=ajax', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.error === 0) {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert('Error: ' + res.message);
                    }
                } catch (e) {
                    alert('An error occurred');
                }
            }
        };
        xhr.send('action=update_priority&id={$TASK_ID}&priority=' + newPriority);
    }
</script>