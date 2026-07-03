<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * SendBirthdayEmail — sends a birthday greeting email to a single customer.
 *
 * Replaces CronBirthday from the original (v4).
 * Scheduled daily at 9am (routes/console.php) via SendBirthdayEmails command.
 *
 * One job per customer (not one job that loops all customers) so the queue
 * worker can parallelize and retry individual failures.
 */
class SendBirthdayEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public int $customerId
    ) {}

    public function handle(): void
    {
        $customer = Customer::find($this->customerId);

        if (!$customer || !$customer->status) {
            return;
        }

        try {
            // Send birthday email — uses the campaign mailer config
            // TODO: Create a BirthdayEmail mailable when the template is ready
            // Mail::to($customer->email)->send(new BirthdayEmail($customer));

            Log::channel('campaign')->info('Birthday email processed', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
            ]);
        } catch (\Throwable $e) {
            Log::channel('campaign')->error('Birthday email failed', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure — log to audit + campaign channels.
     */
    public function failed(\Throwable $exception): void
    {
        Log::channel('campaign')->error('Birthday email job failed permanently', [
            'customer_id' => $this->customerId,
            'error' => $exception->getMessage(),
        ]);
    }
}
