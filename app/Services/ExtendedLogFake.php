<?php

namespace App\Services;

use Timacdonald\LogFake\LogFake;

class ExtendedLogFake extends LogFake
{
    // Get all records (alias for records() method)
    public function getAllRecords()
    {
        return $this->records();
    }
    
    // Set records (for bulk delete)
    public function setRecords($records)
    {
        $reflection = new \ReflectionProperty($this, 'records');
        $reflection->setAccessible(true);
        $reflection->setValue($this, $records);
    }
    
    // Get all records as array (compatible with our view)
    public function getFormattedRecords()
    {
        $records = $this->records();
        $formatted = [];
        
        foreach ($records as $record) {
            if (is_array($record)) {
                $formatted[] = [
                    'level' => $record['level'] ?? 'info',
                    'message' => $record['message'] ?? '',
                    'time' => $record['time'] ?? now()->toDateTimeString(),
                ];
            } elseif (is_object($record)) {
                $formatted[] = [
                    'level' => $record->level ?? 'info',
                    'message' => $record->message ?? '',
                    'time' => $record->time ?? now()->toDateTimeString(),
                ];
            }
        }
        
        return $formatted;
    }
    
    // Get notice count
    public function getNoticeCount()
    {
        return $this->countByLevel('notice');
    }
    
    // Get critical count
    public function getCriticalCount()
    {
        return $this->countByLevel('critical');
    }
}