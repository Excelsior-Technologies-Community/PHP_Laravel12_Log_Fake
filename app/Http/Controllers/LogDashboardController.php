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
    
    // Get live counts for real-time updates
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

    // Main dashboard with enhanced filters
    public function index(Request $request)
    {
        // Use the formatted records
        $records = $this->fakeLogger->getFormattedRecords();
        $logs = collect($records);
        
        // Search filter
        if ($request->search) {
            $logs = $logs->filter(function ($log) use ($request) {
                return str_contains(
                    strtolower($log['message']),
                    strtolower($request->search)
                );
            });
        }
        
        // Level filter
        if ($request->level) {
            $logs = $logs->filter(function ($log) use ($request) {
                return $log['level'] == $request->level;
            });
        }
        
        // Date range filter
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
        
        // Sorting (newest first by default)
        $logs = $logs->sortByDesc('time');
        $logs = $logs->values();
        
        // Pagination
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $paginatedLogs = $logs->forPage($currentPage, $perPage);
        $total = $logs->count();
        $lastPage = ceil($total / $perPage);
        
        // Statistics for chart
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
        ]);
    }
    
    // Generate demo logs
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
            ]
        ];
        
        // Generate 10-20 random logs
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
    
    // Clear all logs
    public function clear()
    {
        $this->fakeLogger->clear();
        
        return redirect('/logs')
            ->with('success', 'All logs cleared successfully.');
    }
    
    // Bulk delete selected logs
    public function bulkDelete(Request $request)
    {
        $indices = json_decode($request->input('indices', '[]'), true);
        
        if (empty($indices)) {
            return redirect('/logs')
                ->with('error', 'No logs selected for deletion.');
        }
        
        $records = $this->fakeLogger->records();
        
        // Convert to array if needed
        $recordsArray = [];
        foreach ($records as $record) {
            if (is_object($record)) {
                $recordsArray[] = (array)$record;
            } else {
                $recordsArray[] = $record;
            }
        }
        
        // Remove selected indices (sort descending to avoid index shifting)
        rsort($indices);
        foreach ($indices as $index) {
            if (isset($recordsArray[$index])) {
                unset($recordsArray[$index]);
            }
        }
        
        // Re-index and save
        $this->fakeLogger->setRecords(array_values($recordsArray));
        
        return redirect('/logs')
            ->with('success', count($indices) . ' log(s) deleted successfully.');
    }
    
    // Export as JSON
    public function export()
    {
        $records = $this->fakeLogger->getFormattedRecords();
        
        return response()->json([
            'status' => true,
            'export_date' => now()->toDateTimeString(),
            'total_logs' => count($records),
            'logs' => $records,
        ], 200, [
            'Content-Disposition' => 'attachment; filename="logs_' . date('Y-m-d_H-i-s') . '.json"'
        ]);
    }
    
    // Export as CSV
    public function exportCsv()
    {
        $records = $this->fakeLogger->getFormattedRecords();
        
        $filename = 'logs_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'w');
        
        // Add headers
        fputcsv($handle, ['Level', 'Message', 'Time']);
        
        // Add data
        foreach ($records as $record) {
            fputcsv($handle, [
                strtoupper($record['level']),
                $record['message'],
                $record['time']
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
    
    // Export as TXT
    public function exportTxt()
    {
        $records = $this->fakeLogger->getFormattedRecords();
        
        $content = "=== LOG EXPORT - " . date('Y-m-d H:i:s') . " ===\n";
        $content .= "Total Logs: " . count($records) . "\n";
        $content .= str_repeat('=', 60) . "\n\n";
        
        foreach ($records as $index => $record) {
            $content .= sprintf(
                "[%d] %s | %s\n    Message: %s\n\n",
                $index + 1,
                strtoupper($record['level']),
                $record['time'],
                $record['message']
            );
        }
        
        return response($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="logs_' . date('Y-m-d_H-i-s') . '.txt"',
        ]);
    }
}