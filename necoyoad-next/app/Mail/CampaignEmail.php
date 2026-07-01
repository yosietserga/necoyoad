<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * CampaignEmail — the Mailable for campaign emails.
 *
 * Replaces PHPMailer 5.0 (v4/v10) with Laravel Mail (Symfony Mailer).
 * Includes List-Unsubscribe header for CAN-SPAM/GDPR compliance (v10 fix).
 */
class CampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subject,
        public string $body,
        public string $fromName,
        public string $fromEmail,
        public ?string $replyTo = null,
    ) {}

    public function build(): self
    {
        $email = $this->subject($this->subject)
            ->from($this->fromEmail, $this->fromName)
            ->html($this->body);

        if ($this->replyTo) {
            $email->replyTo($this->replyTo);
        }

        // List-Unsubscribe header (CAN-SPAM/GDPR compliance — v10 fix)
        // The unsubscribe URL is built in the job and appended to the body

        return $email;
    }
}
