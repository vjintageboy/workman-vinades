<div class="card border-primary border-3 border-bottom-0 border-start-0 border-end-0 mb-3">
    <div class="card-header d-flex justify-content-between align-items-center py-2">
        <span class="fw-medium fs-5"><i class="fa-solid fa-chart-bar"></i> {$LANG->getModule('reports')}</span>
        <a href="{$URL_BACK}" class="btn btn-sm btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> {$LANG->getGlobal('back')}
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg">
        <div class="card bg-secondary text-white h-100">
            <div class="card-body text-center py-3">
                <h2 class="mb-1">{$STATS.total}</h2>
                <small>{$LANG->getModule('total')}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card bg-secondary text-white h-100">
            <div class="card-body text-center py-3">
                <h2 class="mb-1">{$STATS.draft}</h2>
                <small>{$LANG->getModule('status_draft')}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card bg-info text-white h-100">
            <div class="card-body text-center py-3">
                <h2 class="mb-1">{$STATS.pending}</h2>
                <small>{$LANG->getModule('status_pending')}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body text-center py-3">
                <h2 class="mb-1">{$STATS.doing}</h2>
                <small>{$LANG->getModule('status_doing')}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card bg-primary text-white h-100">
            <div class="card-body text-center py-3">
                <h2 class="mb-1">{$STATS.review}</h2>
                <small>{$LANG->getModule('status_review')}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card bg-success text-white h-100">
            <div class="card-body text-center py-3">
                <h2 class="mb-1">{$STATS.done}</h2>
                <small>{$LANG->getModule('status_done')}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="card bg-danger text-white h-100">
            <div class="card-body text-center py-3">
                <h2 class="mb-1">{$STATS.cancelled}</h2>
                <small>{$LANG->getModule('status_cancelled')}</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Stats by User -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fa-solid fa-users"></i> {$LANG->getModule('stats_by_user')}
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{$LANG->getModule('assignee')}</th>
                            <th class="text-center">Pending</th>
                            <th class="text-center">Doing</th>
                            <th class="text-center">Review</th>
                            <th class="text-center">Done</th>
                            <th class="text-center">{$LANG->getModule('total')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if empty($STATS_BY_USER)}
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">{$LANG->getModule('no_data')}</td>
                        </tr>
                        {else}
                        {foreach $STATS_BY_USER as $USER_STAT}
                        <tr>
                            <td><strong>{$USER_STAT.fullname}</strong></td>
                            <td class="text-center"><span class="badge bg-info">{$USER_STAT.pending}</span></td>
                            <td class="text-center"><span class="badge bg-warning text-dark">{$USER_STAT.doing}</span></td>
                            <td class="text-center"><span class="badge bg-primary">{$USER_STAT.review}</span></td>
                            <td class="text-center"><span class="badge bg-success">{$USER_STAT.done}</span></td>
                            <td class="text-center"><strong>{$USER_STAT.total}</strong></td>
                        </tr>
                        {/foreach}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Stats by Category -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fa-solid fa-folder"></i> {$LANG->getModule('stats_by_category')}
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{$LANG->getModule('category')}</th>
                            <th class="text-center">Pending</th>
                            <th class="text-center">Doing</th>
                            <th class="text-center">Done</th>
                            <th class="text-center">{$LANG->getModule('total')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if empty($STATS_BY_CATEGORY)}
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">{$LANG->getModule('no_category')}</td>
                        </tr>
                        {else}
                        {foreach $STATS_BY_CATEGORY as $CAT_STAT}
                        <tr>
                            <td>
                                <span class="d-inline-block rounded me-2" style="width: 12px; height: 12px; background-color: {$CAT_STAT.color};"></span>
                                <strong>{$CAT_STAT.title}</strong>
                            </td>
                            <td class="text-center">{$CAT_STAT.pending}</td>
                            <td class="text-center">{$CAT_STAT.doing}</td>
                            <td class="text-center">{$CAT_STAT.done}</td>
                            <td class="text-center"><strong>{$CAT_STAT.total}</strong></td>
                        </tr>
                        {/foreach}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <!-- Overdue Tasks -->
    <div class="col-lg-6">
        <div class="card border-danger h-100">
            <div class="card-header bg-danger text-white">
                <i class="fa-solid fa-exclamation-triangle"></i> {$LANG->getModule('overdue_tasks')}
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{$LANG->getModule('title')}</th>
                            <th class="text-center">{$LANG->getModule('due_date')}</th>
                            <th class="text-center">{$LANG->getModule('overdue')}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {if empty($OVERDUE_TASKS)}
                        <tr>
                            <td colspan="4" class="text-center text-success py-4">
                                <i class="fa-solid fa-check"></i> {$LANG->getModule('no_overdue_tasks')}
                            </td>
                        </tr>
                        {else}
                        {foreach $OVERDUE_TASKS as $OVERDUE}
                        <tr>
                            <td>{$OVERDUE.title}</td>
                            <td class="text-center text-danger">{$OVERDUE.due_date_formatted}</td>
                            <td class="text-center"><span class="badge bg-danger">{$OVERDUE.days_overdue} {$LANG->getModule('days')}</span></td>
                            <td>
                                <a href="{$OVERDUE.url_edit}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-edit"></i>
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
    
    <!-- Recent Activities -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fa-solid fa-history"></i> {$LANG->getModule('recent_activities')}
            </div>
            <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                {if empty($RECENT_ACTIVITIES)}
                <p class="text-center text-muted py-4">{$LANG->getModule('no_activity')}</p>
                {else}
                <ul class="list-unstyled mb-0">
                    {foreach $RECENT_ACTIVITIES as $ACTIVITY}
                    <li class="py-2 border-bottom">
                        <strong>{$ACTIVITY.user_fullname}</strong>
                        <span class="text-muted">{$ACTIVITY.action_text}</span>
                        <em>"{$ACTIVITY.work_title}"</em>
                        <br>
                        <small class="text-muted"><i class="fa-solid fa-clock"></i> {$ACTIVITY.created_at_formatted}</small>
                    </li>
                    {/foreach}
                </ul>
                {/if}
            </div>
        </div>
    </div>
</div>
