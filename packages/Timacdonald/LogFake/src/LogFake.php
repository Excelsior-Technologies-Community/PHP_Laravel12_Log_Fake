<?php

namespace Timacdonald\LogFake;

use Psr\Log\LoggerInterface;
use Stringable;

class LogFake implements LoggerInterface
{
    protected array $records = [];

    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->write('emergency', $message, $context);
    }

    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->write('alert', $message, $context);
    }

    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->write('critical', $message, $context);
    }

    public function error(Stringable|string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->write('warning', $message, $context);
    }

    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->write('notice', $message, $context);
    }

    public function info(Stringable|string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->write('debug', $message, $context);
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->write($level, $message, $context);
    }

    protected function write($level, $message, array $context = []): void
    {
        $records = session()->get('fake_logs', []);

        $records[] = [
            'level' => $level,
            'message' => (string) $message,
            'context' => $context,
            'time' => now()->format('d M Y h:i:s A'),
        ];

        session()->put('fake_logs', $records);

        $this->records = $records;
    }

    public function records(): array
    {
        return session()->get('fake_logs', []);
    }

    public function clear(): void
    {
        session()->forget('fake_logs');

        $this->records = [];
    }

    public function countByLevel($level): int
    {
        $records = session()->get('fake_logs', []);

        return count(array_filter($records, function ($record) use ($level) {
            return $record['level'] === $level;
        }));
    }

    public function assertLogged($level, $message = null): bool
    {
        $records = session()->get('fake_logs', []);

        foreach ($records as $record) {

            if (
                $record['level'] === $level &&
                ($message === null || str_contains($record['message'], $message))
            ) {
                return true;
            }
        }

        throw new \Exception("Log [$level] containing [$message] not found.");
    }
}