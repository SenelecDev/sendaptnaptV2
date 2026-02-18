<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class WorkflowNotification extends Notification
{
    public string $type;
    public string $title;
    public string $message;
    public ?string $actionUrl;
    public ?string $actionText;
    public array $data;

    /**
     * Types disponibles:
     * - dapt_created, dapt_accepted, dapt_returned, dapt_rejected
     * - napt_submitted, napt_verified, napt_validated, napt_returned, napt_executed, napt_cancelled
     * - interim_assigned, interim_ended
     * - feedback_response
     */
    public function __construct(
        string $type,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?string $actionText = null,
        array $data = []
    ) {
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText ?? 'Voir les détails';
        $this->data = $data;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->email) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Handle failed notification (log mail errors without breaking database notifications)
     */
    public function failed(\Exception $e): void
    {
        Log::error('WorkflowNotification failed: ' . $e->getMessage(), [
            'type' => $this->type,
            'title' => $this->title,
        ]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('[SENELEC - NAPT] ' . $this->title)
            ->greeting('Bonjour ' . ($notifiable->prenom ?? $notifiable->name) . ',');
        
        // Ajouter une icône selon le type
        $icon = $this->getStatusIcon();
        $mail->line(new HtmlString('<strong style="color: #2B1444; font-size: 16px;">' . $icon . ' ' . $this->title . '</strong>'));
        $mail->line($this->message);
        
        // Ajouter des détails supplémentaires si disponibles
        if (!empty($this->data)) {
            $mail->line(new HtmlString('<br><strong style="color: #E85D04;">Informations :</strong>'));
            
            if (isset($this->data['numero'])) {
                $mail->line('• Numéro : ' . $this->data['numero']);
            }
            if (isset($this->data['demande_numero'])) {
                $mail->line('• DAPT associée : ' . $this->data['demande_numero']);
            }
            if (isset($this->data['date_debut'])) {
                $mail->line('• Début : ' . $this->data['date_debut']);
            }
            if (isset($this->data['date_fin'])) {
                $mail->line('• Fin : ' . $this->data['date_fin']);
            }
            if (isset($this->data['lieu'])) {
                $mail->line('• Lieu : ' . $this->data['lieu']);
            }
            if (isset($this->data['motif'])) {
                $mail->line(new HtmlString('<br><em style="color: #666;">Motif : ' . $this->data['motif'] . '</em>'));
            }
        }
        
        if ($this->actionUrl) {
            $mail->action($this->actionText, url($this->actionUrl));
        }
        
        $mail->line(new HtmlString('<br>Merci d\'utiliser l\'application <strong>SENDAPTNAPT</strong>.'));
        $mail->salutation(new HtmlString('Cordialement,<br><strong style="color: #2B1444;">DESA/DESE - SENELEC</strong>'));
        
        return $mail;
    }

    /**
     * Obtenir l'icône selon le type de notification
     */
    protected function getStatusIcon(): string
    {
        return match(true) {
            str_contains($this->type, 'accepted') || str_contains($this->type, 'validated') || str_contains($this->type, 'verified') => '✅',
            str_contains($this->type, 'returned') => '🔄',
            str_contains($this->type, 'rejected') || str_contains($this->type, 'cancelled') => '❌',
            str_contains($this->type, 'executed') => '✔️',
            str_contains($this->type, 'created') || str_contains($this->type, 'submitted') => '📝',
            str_contains($this->type, 'interim_assigned') => '👤',
            str_contains($this->type, 'interim_ended') => '🔚',
            str_contains($this->type, 'feedback') => '💬',
            default => '📋',
        };
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
            'data' => $this->data,
        ];
    }

    /**
     * Icône selon le type de notification
     */
    public static function getIcon(string $type): string
    {
        return match(true) {
            str_starts_with($type, 'dapt_') => 'document-text',
            str_starts_with($type, 'napt_') => 'clipboard-document-check',
            str_starts_with($type, 'interim_') => 'user-group',
            str_starts_with($type, 'feedback_') => 'chat-bubble-left-right',
            default => 'bell',
        };
    }

    /**
     * Couleur selon le type de notification
     */
    public static function getColor(string $type): string
    {
        return match(true) {
            str_contains($type, 'accepted') || str_contains($type, 'validated') || str_contains($type, 'verified') => 'green',
            str_contains($type, 'returned') || str_contains($type, 'rejected') || str_contains($type, 'cancelled') => 'red',
            str_contains($type, 'executed') => 'blue',
            str_contains($type, 'created') || str_contains($type, 'submitted') => 'purple',
            str_contains($type, 'interim_') => 'amber',
            default => 'gray',
        };
    }
}
