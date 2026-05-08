<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class LogFakeTest extends TestCase
{
    public function test_fake_log_records_messages()
    {
        Log::swap(app('log.fake'));

        Log::info('User logged in');

        $fake = app('log.fake');

        $this->assertTrue(
            $fake->assertLogged('info', 'User logged in')
        );
    }

    public function test_fake_log_can_store_multiple_levels()
    {
        Log::swap(app('log.fake'));

        Log::warning('Low storage');

        Log::error('Payment failed');

        $fake = app('log.fake');

        $this->assertTrue(
            $fake->assertLogged('warning', 'Low storage')
        );

        $this->assertTrue(
            $fake->assertLogged('error', 'Payment failed')
        );
    }
}