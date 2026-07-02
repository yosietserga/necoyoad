<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendBirthdayEmail;
use App\Models\Customer;
use Illuminate\Console\Command;

/**
 * SendBirthdayEmails — dispatches SendBirthdayEmail jobs for customers
 * whose birthday is today.
 *
 * Scheduled daily at 09:00 via routes/console.php.
 */
class SendBirthdayEmails extends Command
{
    protected $signature = 'campaigns:send-birthdays';
    protected $description = 'Send birthday greeting emails to customers born today';

    public function handle(): int
    {
        $today = now()->format('m-d');

        $customers = Customer::whereNotNull('birthday')
            ->whereRaw("DATE_FORMAT(birthday, '%m-%d') = ?", [$today])
            ->where('status', true)
            ->get();

        if ($customers->isEmpty()) {
            $this->info('No birthdays today.');
            return self::SUCCESS;
        }

        foreach ($customers as $customer) {
            SendBirthdayEmail::dispatch($customer->id);
        }

        $this->info("Dispatched {$customers->count()} birthday emails.");
        return self::SUCCESS;
    }
}
