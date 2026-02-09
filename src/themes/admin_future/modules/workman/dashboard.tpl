{* Dashboard Template - Workman Module *}
{* Enhanced with Chart.js and new widgets *}

<div class="card border-primary border-3 border-bottom-0 border-start-0 border-end-0 mb-3">
    <div class="card-header d-flex justify-content-between align-items-center py-2">
        <span class="fw-medium fs-5"><i class="fa-solid fa-chart-bar"></i> {$LANG->getModule('dashboard')}</span>
        <a href="{$URL_BACK}" class="btn btn-sm btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> {$LANG->getGlobal('back')}
        </a>
    </div>
</div>

{* Completion Rate Progress Bar *}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0"><i class="fa-solid fa-trophy text-warning"></i> Tỷ lệ hoàn thành</h6>
            <span class="badge bg-success fs-6">{$COMPLETION_RATE}%</span>
        </div>
        <div class="progress" style="height: 25px;">
            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                 role="progressbar" 
                 style="width: {$COMPLETION_RATE}%;" 
                 aria-valuenow="{$COMPLETION_RATE}" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
                {$DONE_TASKS} / {$ACTIVE_TASKS} công việc
            </div>
        </div>
    </div>
</div>

{* Stats Cards *}
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

{* Charts Row *}
<div class="row g-3 mb-4">
    {* Pie Chart - Status Distribution *}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fa-solid fa-chart-pie"></i> Phân bổ theo trạng thái
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="statusChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>
    
    {* Bar Chart - Priority Distribution *}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fa-solid fa-chart-bar"></i> Phân bổ theo độ ưu tiên
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="priorityChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {* Stats by User *}
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
    
    {* Stats by Category *}
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
    {* Upcoming Deadlines - NEW *}
    <div class="col-lg-6">
        <div class="card border-warning h-100">
            <div class="card-header bg-warning text-dark">
                <i class="fa-solid fa-clock"></i> Sắp đến hạn (7 ngày tới)
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{$LANG->getModule('title')}</th>
                            <th class="text-center">Người thực hiện</th>
                            <th class="text-center">Còn lại</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {if empty($UPCOMING_TASKS)}
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fa-solid fa-check"></i> Không có công việc sắp đến hạn
                            </td>
                        </tr>
                        {else}
                        {foreach $UPCOMING_TASKS as $UPCOMING}
                        <tr>
                            <td>{$UPCOMING.title|truncate:40}</td>
                            <td class="text-center"><small>{$UPCOMING.assigned_name}</small></td>
                            <td class="text-center">
                                {if $UPCOMING.days_remaining <= 1}
                                <span class="badge bg-danger">{$UPCOMING.days_remaining} ngày</span>
                                {elseif $UPCOMING.days_remaining <= 3}
                                <span class="badge bg-warning text-dark">{$UPCOMING.days_remaining} ngày</span>
                                {else}
                                <span class="badge bg-info">{$UPCOMING.days_remaining} ngày</span>
                                {/if}
                            </td>
                            <td>
                                <a href="{$UPCOMING.url_edit}" class="btn btn-sm btn-outline-secondary">
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

    {* Overdue Tasks *}
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
                            <td>{$OVERDUE.title|truncate:40}</td>
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
</div>

{* Recent Activities *}
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card">
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

{* Chart.js CDN and Scripts *}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status Pie/Doughnut Chart
    const statusData = {$CHART_DATA};
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusData.labels,
            datasets: [{
                data: statusData.data,
                backgroundColor: statusData.colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Priority Bar Chart
    const priorityData = {$PRIORITY_CHART_DATA};
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    new Chart(priorityCtx, {
        type: 'bar',
        data: {
            labels: priorityData.labels,
            datasets: [{
                label: 'Số công việc',
                data: priorityData.data,
                backgroundColor: priorityData.colors,
                borderRadius: 5,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
