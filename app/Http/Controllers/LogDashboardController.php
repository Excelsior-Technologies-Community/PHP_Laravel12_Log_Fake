<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogDashboardController extends Controller
{
    public function index(Request $request)
    {
        $fake = app('log.fake');

        Log::swap($fake);

        $logs = collect($fake->records());

        // Search Logs
        if ($request->search) {

            $logs = $logs->filter(function ($log) use ($request) {

                return str_contains(
                    strtolower($log['message']),
                    strtolower($request->search)
                );
            });
        }

        // Filter By Level
        if ($request->level) {

            $logs = $logs->filter(function ($log) use ($request) {

                return $log['level'] == $request->level;
            });
        }

        return view('logs.dashboard', [

            'logs' => $logs,

            'infoCount' => $fake->countByLevel('info'),

            'warningCount' => $fake->countByLevel('warning'),

            'errorCount' => $fake->countByLevel('error'),

            'debugCount' => $fake->countByLevel('debug'),
        ]);
    }

    // Generate Demo Logs
    public function generate()
    {
        $fake = app('log.fake');

        Log::swap($fake);

        Log::info('User logged in successfully');

        Log::warning('Disk storage almost full');

        Log::error('Payment gateway timeout');

        Log::debug('Checkout debugging started');

        Log::notice('Profile updated');

        Log::critical('Server CPU usage high');

        return redirect('/logs')
            ->with('success', 'Demo logs generated successfully.');
    }

    // Clear Logs
    public function clear()
    {
        $fake = app('log.fake');

        $fake->clear();

        return redirect('/logs')
            ->with('success', 'Logs cleared successfully.');
    }

    // Export Logs JSON
    public function export()
    {
        $fake = app('log.fake');

        return response()->json([
            'status' => true,
            'logs' => $fake->records(),
        ]);
    }
}