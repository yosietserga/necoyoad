<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled campaign commands — uncomment after creating the corresponding
// Artisan commands in app/Console/Commands/:
//   - SendDueCampaigns      (campaigns:send-due)
//   - ProcessBounces        (campaigns:process-bounces)
//   - SendBirthdayEmails    (campaigns:send-birthdays)
// Schedule::command('campaigns:send-due')->everyFifteenMinutes()->description('Send due email campaigns');
// Schedule::command('campaigns:process-bounces')->hourly()->description('Process email bounces');
// Schedule::command('campaigns:send-birthdays')->dailyAt('09:00')->description('Send birthday greeting emails');
