<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Log Dashboard - Enhanced</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            padding: 24px;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .title h1 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #ffffff;
        }

        .title p {
            color: #94a3b8;
            font-size: 14px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary { background: #3b82f6; color: white; }
        .btn-primary:hover { background: #2563eb; }
        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }
        .btn-warning { background: #f59e0b; color: white; }
        .btn-warning:hover { background: #d97706; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-secondary { background: #334155; color: white; }
        .btn-secondary:hover { background: #475569; }

        .alert {
            padding: 14px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success { background: #065f46; border-left: 4px solid #10b981; }
        .alert-error { background: #991b1b; border-left: 4px solid #ef4444; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: #1e293b;
            padding: 20px;
            border-radius: 16px;
            transition: transform 0.2s;
        }

        .stat-card:hover { transform: translateY(-2px); }

        .stat-card .label {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 36px;
            font-weight: 700;
        }

        .stat-card.info .value { color: #3b82f6; }
        .stat-card.warning .value { color: #f59e0b; }
        .stat-card.error .value { color: #ef4444; }
        .stat-card.debug .value { color: #10b981; }
        .stat-card.total .value { color: #a78bfa; }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 28px;
        }

        @media (max-width: 1000px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }

        .chart-section, .leaderboard-section {
            background: #1e293b;
            border-radius: 16px;
            padding: 20px;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #f1f5f9;
        }

        .chart-bars {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: flex-end;
            margin-bottom: 24px;
        }

        .chart-bar-item {
            flex: 1;
            min-width: 70px;
            text-align: center;
        }

        .bar-container {
            background: #334155;
            border-radius: 8px;
            height: 120px;
            margin-bottom: 8px;
            position: relative;
            overflow: hidden;
        }

        .bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            border-radius: 8px 8px 0 0;
            transition: height 0.3s;
        }

        .bar.info { background: #3b82f6; }
        .bar.warning { background: #f59e0b; }
        .bar.error { background: #ef4444; }
        .bar.debug { background: #10b981; }
        .bar.notice { background: #8b5cf6; }
        .bar.critical { background: #ec489a; }

        .bar-label { font-size: 12px; font-weight: 500; margin-top: 6px; }
        .bar-count { font-size: 14px; font-weight: 600; margin-top: 4px; }

        .timeline-chart {
            display: flex;
            align-items: flex-end;
            gap: 3px;
            height: 80px;
        }

        .timeline-bar-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            justify-content: flex-end;
            position: relative;
        }

        .timeline-bar {
            width: 100%;
            background: #3b82f6;
            border-radius: 3px 3px 0 0;
            min-height: 2px;
        }

        .timeline-bar-wrap:hover .timeline-bar { background: #60a5fa; }

        .timeline-labels {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #64748b;
            margin-top: 8px;
        }

        .leaderboard-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #334155;
        }

        .leaderboard-item:last-child { border-bottom: none; }

        .leaderboard-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: white;
            flex-shrink: 0;
        }

        .leaderboard-count {
            background: #334155;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .filters-card {
            background: #1e293b;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 28px;
        }

        .filters-row {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-group { flex: 1; min-width: 160px; }

        .filter-group label {
            display: block;
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 6px;
        }

        .filter-group input, .filter-group select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #334155;
            background: #0f172a;
            color: #e2e8f0;
            font-size: 14px;
        }

        .filter-group input:focus, .filter-group select:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .level-checkboxes {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            padding: 10px 0;
        }

        .level-checkbox-item {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #0f172a;
            padding: 7px 12px;
            border-radius: 8px;
            border: 1px solid #334155;
            cursor: pointer;
            font-size: 13px;
        }

        .level-checkbox-item input { cursor: pointer; }

        .auto-refresh {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-left: auto;
            flex-wrap: wrap;
        }

        .auto-refresh label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
        }

        .bulk-actions {
            background: #1e293b;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .bulk-actions.show { display: flex; }

        .selected-count { font-size: 14px; color: #94a3b8; }

        .table-container {
            overflow-x: auto;
            background: #1e293b;
            border-radius: 16px;
        }

        table { width: 100%; border-collapse: collapse; }

        th {
            background: #334155;
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            color: #cbd5e1;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid #334155;
            font-size: 14px;
        }

        tr { cursor: pointer; transition: background 0.2s; }
        tr:hover { background: #2d3a4e; }
        tr.selected { background: #3b82f633; }

        .checkbox-col { width: 40px; text-align: center; }

        .checkbox-col input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #3b82f6;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-info { background: #3b82f6; }
        .badge-warning { background: #f59e0b; }
        .badge-error { background: #ef4444; }
        .badge-debug { background: #10b981; }
        .badge-notice { background: #8b5cf6; }
        .badge-critical { background: #ec489a; }

        .message-cell {
            max-width: 420px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .copy-btn {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 12px;
            margin-left: 10px;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .copy-btn:hover { background: #475569; color: white; }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .pagination a, .pagination span {
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: #94a3b8;
            background: #1e293b;
            font-size: 14px;
        }

        .pagination a:hover { background: #3b82f6; color: white; }
        .pagination .active { background: #3b82f6; color: white; }

        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.show { display: flex; }

        .modal-content {
            background: #1e293b;
            border-radius: 20px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #334155;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 { font-size: 18px; }

        .modal-close {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 24px;
            cursor: pointer;
        }

        .modal-close:hover { color: white; }

        .modal-body { padding: 24px; }

        .detail-row { margin-bottom: 20px; }

        .detail-label {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .detail-value {
            font-size: 15px;
            word-break: break-word;
            background: #0f172a;
            padding: 12px;
            border-radius: 10px;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #334155;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 360px;
        }

        .toast {
            background: #1e293b;
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            border-left: 4px solid #ef4444;
            animation: slideIn 0.3s ease-out;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .toast.critical { border-left-color: #ec489a; }

        .toast-icon { font-size: 18px; flex-shrink: 0; }

        .toast-body { flex: 1; }

        .toast-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .toast.critical .toast-title { color: #ec489a; }
        .toast:not(.critical) .toast-title { color: #ef4444; }

        .toast-message { font-size: 13px; color: #cbd5e1; }
        .toast-meta { font-size: 11px; color: #64748b; margin-top: 4px; }

        .toast-close {
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            font-size: 16px;
            flex-shrink: 0;
        }

        .toast-close:hover { color: white; }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        .toast.fade-out { animation: fadeOut 0.3s ease-out forwards; }

        @media (max-width: 768px) {
            body { padding: 16px; }
            .title h1 { font-size: 24px; }
            .stat-card .value { font-size: 28px; }
            .message-cell { max-width: 200px; }
            .chart-bar-item { min-width: 50px; }
            .toast-container { left: 12px; right: 12px; max-width: none; }
        }

        select:disabled, button:disabled { opacity: 0.5; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="toast-container" id="toastContainer"></div>

    <div class="container">

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="header">
            <div class="title">
                <h1>Log Dashboard</h1>
                <p>Advanced monitoring system for application logs</p>
            </div>
            <div class="btn-group">
                <a href="/generate-logs" class="btn btn-success">+ Generate Logs</a>
                <a href="/logs/export" class="btn btn-primary">JSON</a>
                <a href="/logs/export-csv" class="btn btn-primary">CSV</a>
                <a href="/logs/export-txt" class="btn btn-primary">TXT</a>
                <form method="POST" action="/logs/clear" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Clear all logs?')">Clear All</button>
                </form>
            </div>
        </div>

        <div class="stats-grid" id="statsGrid">
            <div class="stat-card info">
                <div class="label">INFO</div>
                <div class="value" id="infoCount">{{ $infoCount }}</div>
            </div>
            <div class="stat-card warning">
                <div class="label">WARNING</div>
                <div class="value" id="warningCount">{{ $warningCount }}</div>
            </div>
            <div class="stat-card error">
                <div class="label">ERROR</div>
                <div class="value" id="errorCount">{{ $errorCount }}</div>
            </div>
            <div class="stat-card debug">
                <div class="label">DEBUG</div>
                <div class="value" id="debugCount">{{ $debugCount }}</div>
            </div>
            <div class="stat-card total">
                <div class="label">TOTAL</div>
                <div class="value" id="totalCount">{{ $totalCount }}</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="chart-section">
                <div class="chart-title">Log Distribution</div>
                <div class="chart-bars">
                    <div class="chart-bar-item">
                        <div class="bar-container"><div class="bar info" style="height: {{ min(120, $levelStats['info'] * 3) }}px"></div></div>
                        <div class="bar-label">INFO</div>
                        <div class="bar-count">{{ $levelStats['info'] }}</div>
                    </div>
                    <div class="chart-bar-item">
                        <div class="bar-container"><div class="bar warning" style="height: {{ min(120, $levelStats['warning'] * 3) }}px"></div></div>
                        <div class="bar-label">WARNING</div>
                        <div class="bar-count">{{ $levelStats['warning'] }}</div>
                    </div>
                    <div class="chart-bar-item">
                        <div class="bar-container"><div class="bar error" style="height: {{ min(120, $levelStats['error'] * 3) }}px"></div></div>
                        <div class="bar-label">ERROR</div>
                        <div class="bar-count">{{ $levelStats['error'] }}</div>
                    </div>
                    <div class="chart-bar-item">
                        <div class="bar-container"><div class="bar debug" style="height: {{ min(120, $levelStats['debug'] * 3) }}px"></div></div>
                        <div class="bar-label">DEBUG</div>
                        <div class="bar-count">{{ $levelStats['debug'] }}</div>
                    </div>
                    <div class="chart-bar-item">
                        <div class="bar-container"><div class="bar notice" style="height: {{ min(120, $levelStats['notice'] * 3) }}px"></div></div>
                        <div class="bar-label">NOTICE</div>
                        <div class="bar-count">{{ $levelStats['notice'] }}</div>
                    </div>
                    <div class="chart-bar-item">
                        <div class="bar-container"><div class="bar critical" style="height: {{ min(120, $levelStats['critical'] * 3) }}px"></div></div>
                        <div class="bar-label">CRITICAL</div>
                        <div class="bar-count">{{ $levelStats['critical'] }}</div>
                    </div>
                </div>

                <div class="chart-title">Last 24 Hours</div>
                @php $maxTimeline = max(1, max($hourlyTimeline)); @endphp
                <div class="timeline-chart">
                    @foreach($hourlyTimeline as $hour => $count)
                        <div class="timeline-bar-wrap" title="{{ $hour }} — {{ $count }} logs">
                            <div class="timeline-bar" style="height: {{ $count > 0 ? max(4, ($count / $maxTimeline) * 100) : 2 }}%"></div>
                        </div>
                    @endforeach
                </div>
                <div class="timeline-labels">
                    <span>{{ array_key_first($hourlyTimeline) }}</span>
                    <span>now</span>
                </div>
            </div>

            <div class="leaderboard-section">
                <div class="chart-title">Top Users by Log Count</div>
                @forelse(array_slice($userLogCounts, 0, 8, true) as $userName => $count)
                    <div class="leaderboard-item">
                        <div class="leaderboard-user">
                            <div class="user-avatar">{{ strtoupper(substr($userName, 0, 1)) }}</div>
                            <span>{{ $userName }}</span>
                        </div>
                        <span class="leaderboard-count">{{ $count }}</span>
                    </div>
                @empty
                    <p style="color:#64748b; font-size: 13px;">No data yet.</p>
                @endforelse
            </div>
        </div>

        <div class="filters-card">
            <form method="GET" action="/logs" id="filterForm">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Search Message</label>
                        <input type="text" name="search" placeholder="Search logs..." value="{{ request('search') }}" id="searchInput">
                    </div>
                    <div class="filter-group">
                        <label>User</label>
                        <select name="user_name" id="userSelect">
                            <option value="">All Users</option>
                            @foreach($allUsers as $u)
                                <option value="{{ $u['name'] }}" {{ request('user_name') == $u['name'] ? 'selected' : '' }}>{{ $u['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" id="dateFrom">
                    </div>
                    <div class="filter-group">
                        <label>To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" id="dateTo">
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Apply Filters</button>
                    </div>
                </div>

                <div class="filters-row" style="margin-top: 14px;">
                    <div class="filter-group" style="flex: 3;">
                        <label>Log Levels</label>
                        <div class="level-checkboxes" id="levelCheckboxes">
                            @php $selectedLevels = (array) request('level', []); @endphp
                            @foreach(['info', 'warning', 'error', 'debug', 'notice', 'critical'] as $lvl)
                                <label class="level-checkbox-item">
                                    <input type="checkbox" name="level[]" value="{{ $lvl }}" {{ in_array($lvl, $selectedLevels) ? 'checked' : '' }}>
                                    {{ ucfirst($lvl) }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="auto-refresh">
                        <label>
                            <input type="checkbox" id="autoRefreshToggle"> Auto Refresh (10s)
                        </label>
                        <label>
                            <input type="checkbox" id="alertToggle" checked> Live Alerts
                        </label>
                    </div>
                </div>
            </form>
        </div>

        <div class="bulk-actions" id="bulkActions">
            <div class="selected-count"><span id="selectedCount">0</span> log(s) selected</div>
            <div class="btn-group">
                <button class="btn btn-danger" id="bulkDeleteBtn" onclick="confirmBulkDelete()">Delete Selected</button>
                <button class="btn btn-secondary" onclick="clearSelection()">Cancel</button>
            </div>
        </div>

        <form id="bulkDeleteForm" method="POST" action="/logs/bulk-delete" style="display: none;">
            @csrf
            @method('DELETE')
            <input type="hidden" name="indices" id="bulkIndices">
        </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-col"><input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()"></th>
                        <th>Level</th>
                        <th>Message</th>
                        <th>User</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody id="logsTableBody">
                    @forelse($logs as $log)
                    <tr data-id="{{ $log['id'] }}" onclick="toggleRowSelection(this, event)">
                        <td class="checkbox-col" onclick="event.stopPropagation()">
                            <input type="checkbox" class="row-checkbox" value="{{ $log['id'] }}">
                        </td>
                        <td>
                            <span class="badge badge-{{ $log['level'] }}">{{ strtoupper($log['level']) }}</span>
                        </td>
                        <td class="message-cell" onclick="showLogDetail('{{ addslashes($log['level']) }}', '{{ addslashes($log['message']) }}', '{{ $log['time'] }}', '{{ addslashes($log['user_name'] ?? 'Unknown') }}')">
                            {{ $log['message'] }}
                            <button class="copy-btn" onclick="copyMessage('{{ addslashes($log['message']) }}', event)">Copy</button>
                        </td>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar" style="width:22px;height:22px;font-size:10px;">{{ strtoupper(substr($log['user_name'] ?? 'U', 0, 1)) }}</div>
                                {{ $log['user_name'] ?? 'Unknown' }}
                            </div>
                        </td>
                        <td>{{ $log['time'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px;">
                            No logs found. Click "Generate Logs" to create demo logs.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($total > $perPage)
        <div class="pagination">
            @for($i = 1; $i <= $lastPage; $i++)
                @if($i == $currentPage)
                    <span class="active">{{ $i }}</span>
                @else
                    <a href="?page={{ $i }}{{ request('search') ? '&search='.request('search') : '' }}{{ request('user_name') ? '&user_name='.request('user_name') : '' }}{{ request('date_from') ? '&date_from='.request('date_from') : '' }}{{ request('date_to') ? '&date_to='.request('date_to') : '' }}">
                        {{ $i }}
                    </a>
                @endif
            @endfor
        </div>
        @endif

        <div id="logModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Log Details</h3>
                    <button class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="detail-row">
                        <div class="detail-label">Level</div>
                        <div class="detail-value" id="modalLevel"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Message</div>
                        <div class="detail-value" id="modalMessage"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">User</div>
                        <div class="detail-value" id="modalUser"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Timestamp</div>
                        <div class="detail-value" id="modalTime"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="copyModalMessage()">Copy Message</button>
                    <button class="btn btn-primary" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedIndices = new Set();
        let lastSeenTimestamp = {{ $nowTimestamp }};

        function toggleRowSelection(row, event) {
            const checkbox = row.querySelector('.row-checkbox');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                if (checkbox.checked) {
                    selectedIndices.add(checkbox.value);
                    row.classList.add('selected');
                } else {
                    selectedIndices.delete(checkbox.value);
                    row.classList.remove('selected');
                }
                updateBulkActions();
            }
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAllCheckbox');
            const checkboxes = document.querySelectorAll('.row-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
                const row = checkbox.closest('tr');
                if (selectAll.checked) {
                    selectedIndices.add(checkbox.value);
                    row.classList.add('selected');
                } else {
                    selectedIndices.delete(checkbox.value);
                    row.classList.remove('selected');
                }
            });
            updateBulkActions();
        }

        function updateBulkActions() {
            const count = selectedIndices.size;
            const bulkDiv = document.getElementById('bulkActions');
            const selectedSpan = document.getElementById('selectedCount');

            selectedSpan.textContent = count;

            if (count > 0) {
                bulkDiv.classList.add('show');
            } else {
                bulkDiv.classList.remove('show');
            }
        }

        function clearSelection() {
            selectedIndices.clear();
            document.querySelectorAll('.row-checkbox').forEach(checkbox => {
                checkbox.checked = false;
                checkbox.closest('tr').classList.remove('selected');
            });
            document.getElementById('selectAllCheckbox').checked = false;
            updateBulkActions();
        }

        function confirmBulkDelete() {
            if (selectedIndices.size === 0) {
                alert('No logs selected');
                return;
            }

            if (confirm(`Delete ${selectedIndices.size} log(s)? This cannot be undone.`)) {
                const indices = Array.from(selectedIndices);
                document.getElementById('bulkIndices').value = JSON.stringify(indices);
                document.getElementById('bulkDeleteForm').submit();
            }
        }

        function copyMessage(message, event) {
            event.stopPropagation();
            navigator.clipboard.writeText(message);
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(() => { btn.textContent = originalText; }, 1500);
        }

        let currentModalMessage = '';

        function showLogDetail(level, message, time, userName) {
            document.getElementById('modalLevel').innerHTML = `<span class="badge badge-${level}">${level.toUpperCase()}</span>`;
            document.getElementById('modalMessage').innerHTML = message;
            document.getElementById('modalUser').innerHTML = userName;
            document.getElementById('modalTime').innerHTML = time;
            currentModalMessage = message;
            document.getElementById('logModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('logModal').classList.remove('show');
        }

        function copyModalMessage() {
            navigator.clipboard.writeText(currentModalMessage);
            alert('Message copied to clipboard');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        let autoRefreshInterval = null;
        let isRefreshing = false;

        function startAutoRefresh() {
            if (autoRefreshInterval) clearInterval(autoRefreshInterval);
            autoRefreshInterval = setInterval(() => {
                if (!isRefreshing) refreshCounts();
            }, 10000);
        }

        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
        }

        async function refreshCounts() {
            isRefreshing = true;
            try {
                const response = await fetch('/logs/counts');
                const data = await response.json();

                document.getElementById('infoCount').textContent = data.infoCount;
                document.getElementById('warningCount').textContent = data.warningCount;
                document.getElementById('errorCount').textContent = data.errorCount;
                document.getElementById('debugCount').textContent = data.debugCount;
                document.getElementById('totalCount').textContent = data.totalCount;

                updateChartBar('info', data.infoCount);
                updateChartBar('warning', data.warningCount);
                updateChartBar('error', data.errorCount);
                updateChartBar('debug', data.debugCount);
                updateChartBar('notice', data.noticeCount);
                updateChartBar('critical', data.criticalCount);
            } catch (error) {
                console.error('Failed to refresh counts:', error);
            }
            isRefreshing = false;
        }

        function updateChartBar(level, count) {
            const bar = document.querySelector(`.bar.${level}`);
            if (bar) {
                const height = Math.min(120, count * 3);
                bar.style.height = height + 'px';
            }
        }

        document.getElementById('autoRefreshToggle').addEventListener('change', function(e) {
            if (e.target.checked) startAutoRefresh();
            else stopAutoRefresh();
        });

        let alertPollInterval = null;
        let alertsEnabled = true;

        function startAlertPolling() {
            if (alertPollInterval) clearInterval(alertPollInterval);
            alertPollInterval = setInterval(checkForAlerts, 5000);
        }

        function stopAlertPolling() {
            if (alertPollInterval) {
                clearInterval(alertPollInterval);
                alertPollInterval = null;
            }
        }

        async function checkForAlerts() {
            if (!alertsEnabled) return;
            try {
                const response = await fetch(`/logs/check-alerts?since=${lastSeenTimestamp}`);
                const data = await response.json();

                lastSeenTimestamp = data.latestTimestamp;

                data.alerts.forEach(alert => showToast(alert));
            } catch (error) {
                console.error('Failed to check alerts:', error);
            }
        }

        function showToast(log) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${log.level === 'critical' ? 'critical' : ''}`;

            toast.innerHTML = `
                <div class="toast-icon">${log.level === 'critical' ? '🔥' : '⚠️'}</div>
                <div class="toast-body">
                    <div class="toast-title">${log.level}</div>
                    <div class="toast-message">${escapeHtml(log.message)}</div>
                    <div class="toast-meta">${escapeHtml(log.user_name || 'Unknown')} · ${log.time}</div>
                </div>
                <button class="toast-close" onclick="dismissToast(this.parentElement)">&times;</button>
            `;

            container.appendChild(toast);

            setTimeout(() => dismissToast(toast), 8000);
        }

        function dismissToast(toast) {
            if (!toast || !toast.parentElement) return;
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 300);
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        document.getElementById('alertToggle').addEventListener('change', function(e) {
            alertsEnabled = e.target.checked;
        });

        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            });
        }

        const userSelect = document.getElementById('userSelect');
        const dateFrom = document.getElementById('dateFrom');
        const dateTo = document.getElementById('dateTo');

        if (userSelect) userSelect.addEventListener('change', () => document.getElementById('filterForm').submit());
        if (dateFrom) dateFrom.addEventListener('change', () => document.getElementById('filterForm').submit());
        if (dateTo) dateTo.addEventListener('change', () => document.getElementById('filterForm').submit());

        document.querySelectorAll('#levelCheckboxes input[type="checkbox"]').forEach(cb => {
            cb.addEventListener('change', () => document.getElementById('filterForm').submit());
        });

        window.onclick = function(event) {
            const modal = document.getElementById('logModal');
            if (event.target === modal) closeModal();
        };

        startAlertPolling();
    </script>
</body>
</html>