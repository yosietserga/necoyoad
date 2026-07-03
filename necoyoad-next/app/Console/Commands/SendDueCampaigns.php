<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendCampaignEmail;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\ContactList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * SendDueCampaigns — dispatches SendCampaignEmail jobs for all campaigns
 * that are due to send (date_start <= now <= date_end, status=active).
 *
 * Replaces the original CronSendCampaigns cron job (v4).
 * Scheduled every 15 minutes via routes/console.php.
 */
class SendDueCampaigns extends Command
{
    protected $signature = 'campaigns:send-due';
    protected $description = 'Send due email campaigns by dispatching SendCampaignEmail jobs per recipient';

    public function handle(): int
    {
        $campaigns = Campaign::where('status', true)
            ->where('date_start', '<=', now())
            ->where('date_end', '>=', now())
            ->get();

        if ($campaigns->isEmpty()) {
            $this->info('No due campaigns found.');
            return self::SUCCESS;
        }

        $totalDispatched = 0;

        foreach ($campaigns as $campaign) {
            // Get all active contacts subscribed to the campaign's contact lists
            $contactLists = $campaign->contactLists ?? collect();

            if ($contactLists->isEmpty()) {
                // If no contact lists attached, send to all active contacts
                $contacts = Contact::where('is_active', true)->get();
            } else {
                $contacts = Contact::where('is_active', true)
                    ->whereHas('contactLists', function ($q) use ($contactLists) {
                        $q->whereIn('contact_lists.id', $contactLists->pluck('id'));
                    })
                    ->get();
            }

            foreach ($contacts as $contact) {
                SendCampaignEmail::dispatch($campaign->id, $contact->id);
                $totalDispatched++;
            }

            Log::channel('campaign')->info('Campaign dispatched', [
                'campaign_id' => $campaign->id,
                'recipients' => $contacts->count(),
            ]);
        }

        $this->info("Dispatched {$totalDispatched} campaign emails across {$campaigns->count()} campaigns.");
        return self::SUCCESS;
    }
}
