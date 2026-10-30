<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Sent to YOU when someone submits the contact form. */
class ContactReceived extends Mailable
{
    public function __construct(public ContactMessage $msg) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New portfolio message from ' . $this->msg->name,
            replyTo: [new Address($this->msg->email, $this->msg->name)],
        );
    }

    public function content(): Content { return new Content(view: 'mail.contact-received'); }
}
