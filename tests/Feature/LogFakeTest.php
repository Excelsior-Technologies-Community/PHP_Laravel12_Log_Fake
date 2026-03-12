<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class LogFakeTest extends TestCase
{
    public function testFakeLog()
    {
        Log::swap(app('log.fake'));

        Log::info('User logged in');

        $fake = app('log.fake');

        $this->assertTrue(
            $fake->assertLogged('info', 'User logged in')
        );
    }
}