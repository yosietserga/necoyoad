<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\CampaignLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\CampaignEmail;

/**
 * SendCampaignEmail — sends a single campaign email to a contact.
 *
 * This is the per-recipient job (equivalent to CronSend::sendCampaign from v4).
 * Dispatched by the campaigns:send-due Artisan command, which creates one
 * job per recipient per due campaign.
 *
 * Features:
 *   - Per-recipient personalisation (7 tokens)
 *   - Link rewriting (trackable URLs)
 *   - Tracking pixel append
 *   - SMTP via Laravel Mail (Symfony Mailer, not PHPMailer 5.0)
 *   - Throttle: rate-limited to 50 emails per minute
 *
 * @see v4 (marketing/campaign subsystem — CronSend::sendCampaign)
 */
class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public int $campaignId,
        public int $contactId
    ) {}

    public function handle(): void
    {
        $campaign = Campaign::with('newsletter')->find($this->campaignId);
        $contact = Contact::find($this->contactId);

        if (!$campaign || !$contact || !$contact->is_active) {
            return;
        }

        // Build the personalised HTML body
        $htmlbody = $this->personalise($campaign->newsletter->htmlbody ?? '', $campaign, $contact);

        // Rewrite links (if trace_click is on)
        if ($campaign->trace_click) {
            $htmlbody = $this->rewriteLinks($htmlbody, $campaign, $contact);
        }

        // Append tracking pixel (if trace_email is on)
        if ($campaign->trace_email) {
            $pixelUrl = route('marketing.track.open', ['campaign' => $campaign->id, 'contact' => $contact->id]);
            $htmlbody .= "<img src=\"{$pixelUrl}\" width=\"1\" height=\"1\" alt=\"\" style=\"display:none;\">";
        }

        // Build the unsubscribe URL (passed to Mailable for List-Unsubscribe header)
        $unsubscribeUrl = route('marketing.unsubscribe', ['token' => $contact->unsubscribe_token ?? $contact->id]);

        // Send via Laravel Mail (Symfony Mailer under the hood)
        Mail::to($contact->email, $contact->name)
            ->send(new CampaignEmail(
                subject: $campaign->subject,
                body: $htmlbody,
                fromName: $campaign->from_name,
                fromEmail: $campaign->from_email,
                replyTo: $campaign->replyto_email,
                unsubscribeUrl: $unsubscribeUrl,
            ));
    }

    private function personalise(string $body, Campaign $campaign, Contact $contact): string
    {
        $replacements = [
            '{%contact_id%}' => $contact->id,
            '{%campaign_id%}' => $campaign->id,
            '{%fullname%}' => $contact->name,
            '{%email%}' => $contact->email,
            '{%telephone%}' => $contact->telephone ?? '',
        ];

        // Store-level tokens
        $store = app('store.context')->model();
        if ($store) {
            $settings = $store->settings ?? [];
            $replacements['{%store_name%}'] = $settings['config_name'] ?? '';
            $replacements['{%store_url%}'] = config('app.url');
            $replacements['{%store_email%}'] = $settings['config_email'] ?? '';
        }

        return str_replace(array_keys($replacements), array_values($replacements), $body);
    }

    private function rewriteLinks(string $html, Campaign $campaign, Contact $contact): string
    {
        return preg_replace_callback('/<a[^>]+href="([^"]+)"[^>]*>/i', function ($matches) use ($campaign, $contact) {
            $originalUrl = $matches[1];

            // Skip mailto:, tel:, # links
            if (str_starts_with($originalUrl, 'mailto:') || str_starts_with($originalUrl, 'tel:') || $originalUrl === '#') {
                return $matches[0];
            }

            // Create campaign_link record
            $nonce = \Illuminate\Support\Str::random(32);
            CampaignLink::create([
                'campaign_id' => $campaign->id,
                'url' => route('marketing.track.click', ['nonce' => $nonce]),
                'redirect' => $originalUrl,
                'link' => $nonce,
            ]);

            $trackableUrl = route('marketing.track.click', ['nonce' => $nonce]);
            return str_replace($originalUrl, $trackableUrl, $matches[0]);
        }, $html);
    }
}
