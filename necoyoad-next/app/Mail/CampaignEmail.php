<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

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
        public ?string $unsubscribeUrl = null,
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
        // Actually attach the header to the outgoing mail so email clients
        // can offer a native "Unsubscribe" button.
        if ($this->unsubscribeUrl) {
            $email->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()->addTextHeader('List-Unsubscribe', "<{$this->unsubscribeUrl}>");
                $message->getHeaders()->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            });
        }

        return $email;
    }
}
