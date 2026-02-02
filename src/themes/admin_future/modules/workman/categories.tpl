<div class="card border-primary border-3 border-bottom-0 border-start-0 border-end-0 mb-3">
    <div class="card-header d-flex justify-content-between align-items-center py-2">
        <span class="fw-medium fs-5"><i class="fa-solid fa-folder"></i> {$LANG->getModule('categories')}</span>
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
        
        {if !empty($SUCCESS)}
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {$SUCCESS}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        {/if}
        
        <div class="row g-3">
            <!-- Form thêm/sửa -->
            <div class="col-lg-4">
                <div class="card {if $FORM_DATA.id > 0}border-warning{else}border-primary{/if}">
                    <div class="card-header {if $FORM_DATA.id > 0}bg-warning text-dark{else}bg-primary text-white{/if}">
                        {if $FORM_DATA.id > 0}
                        <i class="fa-solid fa-edit"></i> {$LANG->getModule('edit_category')}
                        {else}
                        <i class="fa-solid fa-plus"></i> {$LANG->getModule('add_category')}
                        {/if}
                    </div>
                    <div class="card-body">
                        <form method="post" action="{$FORM_ACTION}">
                            <input type="hidden" name="id" value="{$FORM_DATA.id}">
                            
                            <div class="mb-3">
                                <label class="form-label">{$LANG->getModule('category_name')} <span class="text-danger">*</span></label>
                                <input type="text" name="title" value="{$FORM_DATA.title}" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{$LANG->getModule('description')}</label>
                                <input type="text" name="description" value="{$FORM_DATA.description}" class="form-control">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{$LANG->getModule('display_color')}</label>
                                <input type="color" name="color" value="{$FORM_DATA.color}" class="form-control form-control-color w-100">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{$LANG->getModule('weight')}</label>
                                <input type="number" name="weight" value="{$FORM_DATA.weight}" class="form-control" min="0">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{$LANG->getModule('status')}</label>
                                <select name="status" class="form-select">
                                    <option value="1" {if $FORM_DATA.status == 1}selected{/if}>{$LANG->getModule('active')}</option>
                                    <option value="0" {if $FORM_DATA.status == 0}selected{/if}>{$LANG->getModule('inactive')}</option>
                                </select>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" name="submit" value="1" class="btn btn-primary flex-grow-1">
                                    <i class="fa-solid fa-save"></i> {$LANG->getGlobal('save')}
                                </button>
                                {if $FORM_DATA.id > 0}
                                <a href="{$FORM_ACTION}" class="btn btn-secondary">{$LANG->getGlobal('cancel')}</a>
                                {/if}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Danh sách -->
            <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">ID</th>
                                <th>{$LANG->getModule('category_name')}</th>
                                <th class="text-center" style="width: 60px;">{$LANG->getModule('color')}</th>
                                <th class="text-center" style="width: 50px;">{$LANG->getModule('weight_short')}</th>
                                <th class="text-center" style="width: 80px;">{$LANG->getModule('task_count')}</th>
                                <th class="text-center" style="width: 80px;">{$LANG->getModule('status')}</th>
                                <th class="text-center" style="width: 100px;">{$LANG->getModule('actions')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {if empty($CATEGORIES)}
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fa-solid fa-folder-open fa-3x opacity-25"></i>
                                    <p class="mt-2">{$LANG->getModule('no_category_yet')}</p>
                                </td>
                            </tr>
                            {else}
                            {foreach $CATEGORIES as $CAT}
                            <tr>
                                <td class="text-center">{$CAT.id}</td>
                                <td>
                                    <strong>{$CAT.title}</strong>
                                    {if $CAT.description}<br><small class="text-muted">{$CAT.description}</small>{/if}
                                </td>
                                <td class="text-center">
                                    <span class="d-inline-block rounded" style="width: 24px; height: 24px; background-color: {$CAT.color};"></span>
                                </td>
                                <td class="text-center">{$CAT.weight}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{$CAT.task_count}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{$CAT.status_class}">{$CAT.status_text}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{$CAT.url_edit}" class="btn btn-sm btn-outline-primary" title="{$LANG->getGlobal('edit')}">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0);" onclick="if(confirm('{$LANG->getModule("confirm_delete_category")}')) location.href='{$CAT.url_delete}';" class="btn btn-sm btn-outline-danger" title="{$LANG->getGlobal('delete')}">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            {/foreach}
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
