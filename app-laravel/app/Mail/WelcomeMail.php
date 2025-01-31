<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $user; // Przechowujemy użytkownika jako tablicę

    /**
     * Tworzymy nową instancję maila
     */
    public function __construct(array $user)
    {
        $this->user = $user; // Przekazujemy użytkownika
    }

    /**
     * Definiujemy nagłówek maila
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Witaj w ' . config('app.name') . '!',
        );
    }

    /**
     * Treść maila
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.welcome',
        );
    }

    /**
     * Załączniki (jeśli są)
     */
    public function attachments(): array
    {
        return [];
    }
}
