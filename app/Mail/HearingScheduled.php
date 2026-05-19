<?php

namespace App\Mail;

use App\Models\CaseHearing;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HearingScheduled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CaseHearing $hearing,
        public string $recipientName,
        public string $recipientEmail,
        public string $role // 'complainant' or 'respondent'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Barangay Bucandala 1 - Hearing Scheduled for Case No. ' . $this->hearing->case->case_no,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.hearing-scheduled',
        );
    }
}
