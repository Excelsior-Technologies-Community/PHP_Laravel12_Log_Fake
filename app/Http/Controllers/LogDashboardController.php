<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogDashboardController extends Controller
{
    protected $fakeLogger;

    public function __construct()
    {
        $this->fakeLogger = app('log.fake');
    }

    public function getCounts()
    {
        return response()->json([
            'infoCount' => $this->fakeLogger->countByLevel('info'),
            'warningCount' => $this->fakeLogger->countByLevel('warning'),
            'errorCount' => $this->fakeLogger->countByLevel('error'),
            'debugCount' => $this->fakeLogger->countByLevel('debug'),
            'noticeCount' => $this->fakeLogger->countByLevel('notice'),
            'criticalCount' => $this->fakeLogger->countByLevel('critical'),
            'totalCount' => count($this->fakeLogger->records()),
        ]);
    }

    public function checkNewAlerts(Request $request)
    {
        $since = (int) $request->get('since', 0);
        $newRecords = $this->fakeLogger->recordsSince($since);

        $alerts = array_values(array_filter($newRecords, function ($record) {
            return in_array($record['level'], ['error', 'critical']);
        }));

        $latestTimestamp = $since;
        foreach ($newRecords as $record) {
            $latestTimestamp = max($latestTimestamp, $record['timestamp'] ?? 0);
        }

        return response()->json([
            'alerts' => $alerts,
            'latestTimestamp' => $latestTimestamp,
        ]);
    }

    public function index(Request $request)
    {
        $records = $this->fakeLogger->getFormattedRecords();

        // purana/incomplete session records (jema 'id' jevi keys missing hoy)
        // ne filter kari kadhi nakho, jethi view ma error na aave
        $records = array_values(array_filter($records, function ($record) {
            return isset($record['id'], $record['level'], $record['message'], $record['time']);
        }));

        $logs = collect($records);

        if ($request->search) {
            $logs = $logs->filter(function ($log) use ($request) {
                return str_contains(
                    strtolower($log['message']),
                    strtolower($request->search)
                );
            });
        }

        if ($request->level) {
            $levels = is_array($request->level) ? $request->level : [$request->level];
            $logs = $logs->filter(function ($log) use ($levels) {
                return in_array($log['level'], $levels);
            });
        }

        if ($request->user_name) {
            $logs = $logs->filter(function ($log) use ($request) {
                return ($log['user_name'] ?? null) === $request->user_name;
            });
        }

        if ($request->date_from) {
            $logs = $logs->filter(function ($log) use ($request) {
                return strtotime($log['time']) >= strtotime($request->date_from);
            });
        }

        if ($request->date_to) {
            $logs = $logs->filter(function ($log) use ($request) {
                return strtotime($log['time']) <= strtotime($request->date_to . ' 23:59:59');
            });
        }

        $logs = $logs->sortByDesc('timestamp');
        $logs = $logs->values();

        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $paginatedLogs = $logs->forPage($currentPage, $perPage);
        $total = $logs->count();
        $lastPage = max(1, ceil($total / $perPage));

        $levelStats = [
            'info' => $this->fakeLogger->countByLevel('info'),
            'warning' => $this->fakeLogger->countByLevel('warning'),
            'error' => $this->fakeLogger->countByLevel('error'),
            'debug' => $this->fakeLogger->countByLevel('debug'),
            'notice' => $this->fakeLogger->countByLevel('notice'),
            'critical' => $this->fakeLogger->countByLevel('critical'),
        ];

        return view('logs.dashboard', [
            'logs' => $paginatedLogs,
            'infoCount' => $this->fakeLogger->countByLevel('info'),
            'warningCount' => $this->fakeLogger->countByLevel('warning'),
            'errorCount' => $this->fakeLogger->countByLevel('error'),
            'debugCount' => $this->fakeLogger->countByLevel('debug'),
            'noticeCount' => $this->fakeLogger->countByLevel('notice'),
            'criticalCount' => $this->fakeLogger->countByLevel('critical'),
            'totalCount' => count($this->fakeLogger->records()),
            'levelStats' => $levelStats,
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
            'total' => $total,
            'perPage' => $perPage,
            'allUsers' => $this->fakeLogger->allUsers(),
            'userLogCounts' => $this->fakeLogger->userLogCounts(),
            'hourlyTimeline' => $this->fakeLogger->hourlyTimeline(),
            'nowTimestamp' => now()->timestamp,
        ]);
    }

    public function generate()
    {
        $messages = [
            'info' => [
                'User authentication successful',
                'Database connection established',
                'Cache cleared successfully',
                'Email notification sent to user',
                'API request completed successfully',
                'Background job processed',
                'Session started for user',
                'Configuration loaded',
            ],
            'warning' => [
                'Disk usage above 80%',
                'Slow query detected',
                'Deprecated function usage',
                'API rate limit approaching',
                'Memory usage high',
                'Failed login attempt from unknown IP',
            ],
            'error' => [
                'Database connection timeout',
                'Payment gateway failed',
                'File upload failed - size limit exceeded',
                'API response 500 error',
                'Email delivery failed',
                'Cache write operation failed',
            ],
            'debug' => [
                'User data retrieval started',
                'Middleware processing request',
                'Query execution time: 45ms',
                'Route matched: dashboard.show',
                'View rendering started',
                'Cache hit for key: user_preferences',
            ],
            'notice' => [
                'User profile updated',
                'Password changed successfully',
                'New device login detected',
                'Settings saved',
                'Cron job executed',
            ],
            'critical' => [
                'System database corrupted',
                'Security breach detected',
                'Application crash detected',
                'Data loss possible',
                'Critical service unavailable',
            ],
        ];

        $count = rand(10, 20);
        for ($i = 0; $i < $count; $i++) {
            $level = array_rand($messages);
            $messageArray = $messages[$level];
            $message = $messageArray[array_rand($messageArray)];

            switch ($level) {
                case 'info':
                    $this->fakeLogger->info($message . ' [ID:' . rand(1000, 9999) . ']');
                    break;
                case 'warning':
                    $this->fakeLogger->warning($message . ' - Code: ' . rand(100, 999));
                    break;
                case 'error':
                    $this->fakeLogger->error($message . ' | Trace: ' . uniqid());
                    break;
                case 'debug':
                    $this->fakeLogger->debug($message . ' [Time: ' . rand(1, 500) . 'ms]');
                    break;
                case 'notice':
                    $this->fakeLogger->notice($message . ' by user_' . rand(1, 50));
                    break;
                case 'critical':
                    $this->fakeLogger->critical($message . ' [Severity: HIGH]');
                    break;
            }
        }

        return redirect('/logs')
            ->with('success', $count . ' demo logs generated successfully.');
    }

    public function clear()
    {
        $this->fakeLogger->clear();

        return redirect('/logs')
            ->with('success', 'All logs cleared successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->input('indices', '[]'), true);

        if (empty($ids)) {
            return redirect('/logs')
                ->with('error', 'No logs selected for deletion.');
        }

        $records = $this->fakeLogger->records();

        $remaining = array_values(array_filter($records, function ($record) use ($ids) {
            return ! in_array($record['id'], $ids);
        }));

        $this->fakeLogger->setRecords($remaining);

        return redirect('/logs')
            ->with('success', count($ids) . ' log(s) deleted successfully.');
    }

    public function export()
    {
        $records = $this->fakeLogger->getFormattedRecords();

        return response()->json([
            'status' => true,
            'export_date' => now()->toDateTimeString(),
            'total_logs' => count($records),
            'logs' => $records,
        ], 200, [
            'Content-Disposition' => 'attachment; filename="logs_' . date('Y-m-d_H-i-s') . '.json"',
        ]);
    }

    public function exportCsv()
    {
        $records = $this->fakeLogger->getFormattedRecords();

        $filename = 'logs_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'w');

        fputcsv($handle, ['Level', 'Message', 'User', 'Time']);

        foreach ($records as $record) {
            fputcsv($handle, [
                strtoupper($record['level']),
                $record['message'],
                $record['user_name'] ?? '',
                $record['time'],
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportTxt()
    {
        $records = $this->fakeLogger->getFormattedRecords();

        $content = "=== LOG EXPORT - " . date('Y-m-d H:i:s') . " ===\n";
        $content .= "Total Logs: " . count($records) . "\n";
        $content .= str_repeat('=', 60) . "\n\n";

        foreach ($records as $index => $record) {
            $content .= sprintf(
                "[%d] %s | %s | User: %s\n    Message: %s\n\n",
                $index + 1,
                strtoupper($record['level']),
                $record['time'],
                $record['user_name'] ?? 'Unknown',
                $record['message']
            );
        }

        return response($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="logs_' . date('Y-m-d_H-i-s') . '.txt"',
        ]);
    }
}