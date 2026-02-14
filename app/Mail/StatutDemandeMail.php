<?php

namespace App\Mail;

use App\Models\Demande;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatutDemandeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Demande $demande;
    public string $customMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(Demande $demande, string $message)
    {
        $this->demande = $demande;
        $this->customMessage = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SENDAPTNAPT - DAPT ' . $this->demande->numero_demande . ' - ' . ucfirst($this->demande->statut),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.statut-demande',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        
        // Joindre le PDF si disponible
        if ($this->demande->pdf_path && file_exists(storage_path('app/public/' . $this->demande->pdf_path))) {
            $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromPath(
                storage_path('app/public/' . $this->demande->pdf_path)
            )->as('DAPT_' . $this->demande->numero_demande . '.pdf')
             ->withMime('application/pdf');
        }
        
        return $attachments;
    }
}
