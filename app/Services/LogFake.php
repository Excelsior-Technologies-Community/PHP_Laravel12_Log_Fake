<?php

namespace App\Services;

class LogFake
{
    protected array $fakeUsers = [
        ['id' => 1, 'name' => 'Aarav Patel', 'email' => 'aarav.patel@example.com'],
        ['id' => 2, 'name' => 'Priya Shah', 'email' => 'priya.shah@example.com'],
        ['id' => 3, 'name' => 'Rohan Mehta', 'email' => 'rohan.mehta@example.com'],
        ['id' => 4, 'name' => 'Diya Joshi', 'email' => 'diya.joshi@example.com'],
        ['id' => 5, 'name' => 'Kabir Desai', 'email' => 'kabir.desai@example.com'],
        ['id' => 6, 'name' => 'Ananya Trivedi', 'email' => 'ananya.trivedi@example.com'],
        ['id' => 7, 'name' => 'Vivaan Sheth', 'email' => 'vivaan.sheth@example.com'],
        ['id' => 8, 'name' => 'Ishita Rao', 'email' => 'ishita.rao@example.com'],
        ['id' => null, 'name' => 'System', 'email' => 'system@internal'],
    ];

    public function emergency($message, array $context = []): void
    {
        $this->write('emergency', $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->write('alert', $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->write('critical', $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->write('warning', $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->write('notice', $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->write('debug', $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $this->write($level, $message, $context);
    }

    protected function write($level, $message, array $context = []): void
    {
        $records = session()->get('fake_logs', []);

        $user = $this->fakeUsers[array_rand($this->fakeUsers)];

        $records[] = [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'level' => $level,
            'message' => (string) $message,
            'context' => $context,
            'time' => now()->format('Y-m-d H:i:s'),
            'timestamp' => now()->timestamp,
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
        ];

        session()->put('fake_logs', $records);
        session()->put('fake_logs_last_id', end($records)['id'] ?? null);
    }

    public function records(): array
    {
        return session()->get('fake_logs', []);
    }

    public function getFormattedRecords(): array
    {
        return $this->records();
    }

    public function setRecords(array $records): void
    {
        session()->put('fake_logs', array_values($records));
    }

    public function clear(): void
    {
        session()->forget('fake_logs');
        session()->forget('fake_logs_last_id');
    }

    public function countByLevel($level): int
    {
        $records = $this->records();

        return count(array_filter($records, fn ($record) => $record['level'] === $level));
    }

    public function allUsers(): array
    {
        return $this->fakeUsers;
    }

    public function recordsSince(int $timestamp): array
    {
        return array_values(array_filter(
            $this->records(),
            fn ($record) => ($record['timestamp'] ?? 0) > $timestamp
        ));
    }

    public function userLogCounts(): array
    {
        $records = $this->records();
        $counts = [];

        foreach ($records as $record) {
            $key = $record['user_name'] ?? 'Unknown';
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        arsort($counts);

        return $counts;
    }

    public function hourlyTimeline(): array
    {
        $records = $this->records();
        $buckets = [];

        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i)->format('H:00');
            $buckets[$hour] = 0;
        }

        foreach ($records as $record) {
            $ts = $record['timestamp'] ?? null;
            if (! $ts) {
                continue;
            }

            $hour = \Carbon\Carbon::createFromTimestamp($ts)->format('H:00');

            if (array_key_exists($hour, $buckets)) {
                $buckets[$hour]++;
            }
        }

        return $buckets;
    }
}