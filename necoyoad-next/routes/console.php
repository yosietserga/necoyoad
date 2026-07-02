<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled campaign commands (commands in app/Console/Commands/)
Schedule::command('campaigns:send-due')->everyFifteenMinutes()->description('Send due email campaigns');
Schedule::command('campaigns:process-bounces')->hourly()->description('Process email bounces');
Schedule::command('campaigns:send-birthdays')->dailyAt('09:00')->description('Send birthday greeting emails');

// Image cache cleanup (daily — removes orphaned thumbnails)
Schedule::command('images:clean-cache')->dailyAt('03:00')->description('Clean orphaned image thumbnails');
