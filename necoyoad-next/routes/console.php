<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send scheduled campaigns (every 15 minutes)
Schedule::command('campaigns:send-due')
    ->everyFifteenMinutes()
    ->description('Send due email campaigns');

// Process bounces (hourly)
Schedule::command('campaigns:process-bounces')
    ->hourly()
    ->description('Process email bounces');

// Send birthday emails (daily at 9am)
Schedule::command('campaigns:send-birthdays')
    ->dailyAt('09:00')
    ->description('Send birthday greeting emails');
