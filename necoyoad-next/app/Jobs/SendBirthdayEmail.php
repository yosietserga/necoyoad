<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * SendBirthdayEmail — sends birthday greeting emails.
 *
 * Replaces CronBirthday from the original (v4).
 * Scheduled daily at 9am (routes/console.php).
 */
class SendBirthdayEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $today = now()->format('m-d');

        $customers = Customer::whereNotNull('birthday')
            ->whereRaw("DATE_FORMAT(birthday, '%m-%d') = ?", [$today])
            ->where('status', true)
            ->get();

        foreach ($customers as $customer) {
            // Send birthday email (template TBD)
            // Mail::to($customer->email)->send(new BirthdayEmail($customer));
        }
    }
}
