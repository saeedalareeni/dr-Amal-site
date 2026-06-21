<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifySubscriber extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public function __construct(public Subscriber $subscriber, public string $verificationUrl) {}
    public function envelope(): Envelope { return new Envelope(subject: $this->subscriber->locale === 'en' ? 'Verify your email' : 'تأكيد البريد الإلكتروني'); }
    public function content(): Content { return new Content(view: 'emails.verify-subscriber'); }
}
