<?php

namespace App\Services;

class FakeLogManager
{
    private static $records = [];
    
    public function log($level, $message, array $context = [])
    {
        self::$records[] = [
            'level' => $level,
            'message' => $message,
            'time' => now()->toDateTimeString(),
            'context' => $context
        ];
        
        // Optional: Also log to actual Laravel log for debugging
        // if (config('app.debug')) {
        //     \Illuminate\Support\Facades\Log::channel('single')->$level($message);
        // }
    }
    
    public function emergency($message, array $context = [])
    {
        $this->log('emergency', $message, $context);
    }
    
    public function alert($message, array $context = [])
    {
        $this->log('alert', $message, $context);
    }
    
    public function critical($message, array $context = [])
    {
        $this->log('critical', $message, $context);
    }
    
    public function error($message, array $context = [])
    {
        $this->log('error', $message, $context);
    }
    
    public function warning($message, array $context = [])
    {
        $this->log('warning', $message, $context);
    }
    
    public function notice($message, array $context = [])
    {
        $this->log('notice', $message, $context);
    }
    
    public function info($message, array $context = [])
    {
        $this->log('info', $message, $context);
    }
    
    public function debug($message, array $context = [])
    {
        $this->log('debug', $message, $context);
    }
    
    public function records()
    {
        return array_reverse(self::$records);
    }
    
    public function getAllRecords()
    {
        return self::$records;
    }
    
    public function clear()
    {
        self::$records = [];
    }
    
    public function countByLevel($level)
    {
        return count(array_filter(self::$records, function($record) use ($level) {
            return $record['level'] === $level;
        }));
    }
    
    public function setRecords($records)
    {
        self::$records = $records;
    }
    
    // Prevent serialization
    public function __sleep()
    {
        return [];
    }
    
    public function __wakeup()
    {
        self::$records = [];
    }
}