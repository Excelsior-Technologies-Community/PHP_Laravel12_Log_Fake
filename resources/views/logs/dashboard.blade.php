<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laravel Log Fake Dashboard</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #0f172a;
            color: white;
            padding: 40px;
        }

        .container {
            max-width: 1400px;
            margin: auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .title h1 {
            font-size: 38px;
            margin-bottom: 8px;
        }

        .title p {
            color: #94a3b8;
        }

        .btn-group {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            font-weight: bold;
        }

        .danger {
            background: #dc2626;
        }

        .export {
            background: #2563eb;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: #1e293b;
            padding: 25px;
            border-radius: 18px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .card h2 {
            font-size: 38px;
            margin-top: 10px;
        }

        .card p {
            color: #cbd5e1;
        }

        .filters {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .filters input,
        .filters select {
            padding: 14px;
            border-radius: 10px;
            border: none;
            width: 260px;
            background: #1e293b;
            color: white;
        }

        .table-box {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #1e293b;
            border-radius: 15px;
            overflow: hidden;
        }

        th {
            background: #334155;
            text-align: left;
            padding: 16px;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #334155;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .badge {
            padding: 7px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }

        .info {
            background: #2563eb;
        }

        .warning {
            background: #f59e0b;
        }

        .error {
            background: #dc2626;
        }

        .debug {
            background: #10b981;
        }

        .notice {
            background: #8b5cf6;
        }

        .critical {
            background: #ef4444;
        }

        .success {
            background: #16a34a;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        @media(max-width:768px) {

            body {
                padding: 20px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .filters input,
            .filters select {
                width: 100%;
            }
        }
    </style>

</head>

<body>

    <div class="container">

        @if(session('success'))

        <div class="success">
            {{ session('success') }}
        </div>

        @endif

        <div class="topbar">

            <div class="title">
                <h1>Laravel Log Fake Dashboard</h1>
                <p>Advanced in-memory fake logger monitoring system</p>
            </div>
            
            <div class="btn-group">

                <a href="/generate-logs" class="btn export">
                    Generate Logs
                </a>

                <a href="/logs/export" class="btn export">
                    Export JSON
                </a>

                <form method="POST" action="/logs/clear">

                    @csrf

                    <button class="btn danger">
                        Clear Logs
                    </button>

                </form>

            </div>

        </div>

        <div class="cards">

            <div class="card">
                <p>Info Logs</p>
                <h2>{{ $infoCount }}</h2>
            </div>

            <div class="card">
                <p>Warning Logs</p>
                <h2>{{ $warningCount }}</h2>
            </div>

            <div class="card">
                <p>Error Logs</p>
                <h2>{{ $errorCount }}</h2>
            </div>

            <div class="card">
                <p>Debug Logs</p>
                <h2>{{ $debugCount }}</h2>
            </div>

        </div>

        <form method="GET" action="/logs">

            <div class="filters">

                <input
                    type="text"
                    name="search"
                    placeholder="Search logs..."
                    value="{{ request('search') }}">

                <select name="level">

                    <option value="">Filter By Level</option>

                    <option value="info">Info</option>

                    <option value="warning">Warning</option>

                    <option value="error">Error</option>

                    <option value="debug">Debug</option>

                    <option value="notice">Notice</option>

                    <option value="critical">Critical</option>

                </select>

                <button class="btn export">
                    Apply Filters
                </button>

            </div>

        </form>

        <div class="table-box">

            <table>

                <thead>

                    <tr>
                        <th>Level</th>
                        <th>Message</th>
                        <th>Time</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($logs as $log)

                    <tr>

                        <td>

                            <span class="badge {{ $log['level'] }}">

                                {{ strtoupper($log['level']) }}

                            </span>

                        </td>

                        <td>
                            {{ $log['message'] }}
                        </td>

                        <td>
                            {{ $log['time'] }}
                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="3">
                            No Logs Found
                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</body>

</html>