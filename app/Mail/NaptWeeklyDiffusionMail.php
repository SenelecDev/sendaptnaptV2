<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class NaptWeeklyDiffusionMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $napts;
    public int $semaine;
    public int $annee;
    public string $groupeNom;
    public ?string $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(Collection $napts, int $semaine, int $annee, string $groupeNom, ?string $pdfPath = null)
    {
        $this->napts = $napts;
        $this->semaine = $semaine;
        $this->annee = $annee;
        $this->groupeNom = $groupeNom;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "NAPT - Diffusion Hebdomadaire Semaine {$this->semaine}/{$this->annee}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.napt-weekly-diffusion',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (!$this->pdfPath || !file_exists($this->pdfPath)) {
            return [];
        }

        return [
            Attachment::fromPath($this->pdfPath)
                ->as("NAPT_Semaine_{$this->semaine}_{$this->annee}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
