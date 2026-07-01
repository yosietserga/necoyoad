<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send scheduled campaigns
Schedule::command('campaigns:send-due')
    ->everyFifteenMinutes()
    ->description('Send due email campaigns');

// Process bounces
Schedule::command('campaigns:process-bounces')
    ->hourly()
    ->description('Process email bounces');

// Send birthday emails
Schedule::command('campaigns:send-birthdays')
    ->dailyAt('09:00')
    ->description('Send birthday greeting emails');
