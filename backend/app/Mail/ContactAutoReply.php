<?php

namespace App\Mail;

use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Optional confirmation sent to the visitor. Text comes from Admin → Site settings → Contact & email. */
class ContactAutoReply extends Mailable
{
    public function __construct(public ContactMessage $msg) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: (string) Setting::get('auto_reply_subject', 'Thanks for reaching out!'));
    }

    public function content(): Content
    {
        $body = str_replace('{name}', $this->msg->name, (string) Setting::get('auto_reply_body', 'Thanks for your message!'));
        return new Content(view: 'mail.auto-reply', with: ['body' => $body]);
    }
}
