<!-- BEGIN: main -->
<div class="workman-filter-block">
    <!-- Status Filter -->
    <div class="filter-section">
        <h5 class="filter-title">
            <i class="fa fa-filter"></i> {LANG.status}
        </h5>
        <ul class="filter-list">
            <!-- BEGIN: status_item -->
            <li class="filter-item {STATUS_ITEM.active}">
                <a href="{STATUS_ITEM.url}">
                    <span class="filter-icon" style="color: {STATUS_ITEM.color};">
                        <i class="fa {STATUS_ITEM.icon}"></i>
                    </span>
                    <span class="filter-label">{STATUS_ITEM.label}</span>
                    <span class="filter-count">{STATUS_ITEM.count}</span>
                </a>
            </li>
            <!-- END: status_item -->
        </ul>
    </div>
    
    <!-- BEGIN: category_section -->
    <div class="filter-section">
        <h5 class="filter-title">
            <i class="fa fa-folder"></i> {LANG.category}
        </h5>
        <ul class="filter-list">
            <li class="filter-item {CATEGORY_ALL_ACTIVE}">
                <a href="{CLEAR_CATEGORY_URL}">
                    <span class="filter-icon"><i class="fa fa-th-large"></i></span>
                    <span class="filter-label">{LANG.all}</span>
                </a>
            </li>
            <!-- BEGIN: category_item -->
            <li class="filter-item {CATEGORY_ITEM.active}">
                <a href="{CATEGORY_ITEM.url}">
                    <span class="filter-dot" style="background-color: {CATEGORY_ITEM.color};"></span>
                    <span class="filter-label">{CATEGORY_ITEM.title}</span>
                </a>
            </li>
            <!-- END: category_item -->
        </ul>
    </div>
    <!-- END: category_section -->
    
    <!-- Priority Filter -->
    <div class="filter-section">
        <h5 class="filter-title">
            <i class="fa fa-flag"></i> {LANG.priority}
        </h5>
        <ul class="filter-list">
            <!-- BEGIN: priority_item -->
            <li class="filter-item {PRIORITY_ITEM.active}">
                <a href="{PRIORITY_ITEM.url}">
                    <span class="filter-icon" style="color: {PRIORITY_ITEM.color};">
                        <i class="fa {PRIORITY_ITEM.icon}"></i>
                    </span>
                    <span class="filter-label">{PRIORITY_ITEM.label}</span>
                </a>
            </li>
            <!-- END: priority_item -->
        </ul>
    </div>
    
    <!-- Clear All -->
    <div class="filter-actions">
        <a href="{CLEAR_ALL_URL}" class="btn btn-sm btn-default btn-block">
            <i class="fa fa-refresh"></i> {LANG.clear_filter}
        </a>
    </div>
</div>

<!-- <style>
.workman-filter-block {
    background: #fff;
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.filter-section {
    margin-bottom: 20px;
}

.filter-section:last-of-type {
    margin-bottom: 15px;
}

.filter-title {
    font-size: 13px;
    font-weight: 600;
    color: #7f8c8d;
    text-transform: uppercase;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #eee;
}

.filter-title i {
    margin-right: 6px;
}

.filter-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.filter-item {
    margin-bottom: 2px;
}

.filter-item a {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    border-radius: 8px;
    color: #555;
    text-decoration: none;
    transition: all 0.2s ease;
}

.filter-item a:hover {
    background: #f8f9fa;
    color: #3498db;
}

.filter-item.active a {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: #fff;
}

.filter-item.active .filter-icon {
    color: #fff !important;
}

.filter-item.active .filter-count {
    background: rgba(255,255,255,0.2);
    color: #fff;
}

.filter-icon {
    width: 20px;
    text-align: center;
    margin-right: 10px;
    font-size: 14px;
}

.filter-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 10px;
    flex-shrink: 0;
}

.filter-label {
    flex: 1;
    font-size: 13px;
}

.filter-count {
    background: #e8e8e8;
    color: #666;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    min-width: 24px;
    text-align: center;
}

.filter-actions {
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.filter-actions .btn {
    border-radius: 8px;
}
</style> -->
<!-- END: main -->
