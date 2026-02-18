<?php

namespace App\Services;

use App\Models\Demande;
use App\Models\Note;
use App\Models\Absence;
use App\Models\Observation;
use App\Models\User;
use App\Notifications\WorkflowNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Send notification safely - database always saved, mail errors logged
     */
    protected function sendSafe($notifiables, WorkflowNotification $notification): void
    {
        try {
            if ($notifiables instanceof User) {
                $notifiables->notify($notification);
            } else {
                Notification::send($notifiables, $notification);
            }
        } catch (\Exception $e) {
            Log::error('Notification send failed: ' . $e->getMessage(), [
                'type' => class_basename($notification),
            ]);

            try {
                $dbNotification = new WorkflowNotification(
                    type: $notification->type ?? 'error',
                    title: $notification->title ?? 'Notification',
                    message: $notification->message ?? '',
                    actionUrl: $notification->actionUrl ?? null,
                    actionText: $notification->actionText ?? null,
                    data: $notification->data ?? []
                );
                // Force database-only channel by wrapping
                if ($notifiables instanceof User) {
                    $notifiables->notifyNow($dbNotification, ['database']);
                } else {
                    Notification::sendNow($notifiables, $dbNotification, ['database']);
                }
            } catch (\Exception $dbError) {
                Log::error('Database notification also failed: ' . $dbError->getMessage());
            }
        }
    }

    // ==================== DAPT NOTIFICATIONS ====================

    /**
     * Notifier la création d'une DAPT
     */
    public function notifyDaptCreated(Demande $demande): void
    {
        $desaUsers = User::role('desa')->get();
        
        $this->sendSafe($desaUsers, new WorkflowNotification(
            type: 'dapt_created',
            title: 'Nouvelle DAPT créée',
            message: "Une nouvelle demande {$demande->numero_demande} a été créée par {$demande->demandeur->full_name}.",
            actionUrl: "/desa/demandes/{$demande->id}",
            actionText: 'Traiter la demande',
            data: ['demande_id' => $demande->id, 'numero' => $demande->numero_demande]
        ));
    }

    /**
     * Notifier l'acceptation d'une DAPT
     */
    public function notifyDaptAccepted(Demande $demande): void
    {
        if ($demande->demandeur) {
            $this->sendSafe($demande->demandeur, new WorkflowNotification(
                type: 'dapt_accepted',
                title: 'DAPT acceptée',
                message: "Votre demande {$demande->numero_demande} a été acceptée.",
                actionUrl: "/demandeur/demandes/{$demande->id}",
                actionText: 'Voir la demande',
                data: ['demande_id' => $demande->id, 'numero' => $demande->numero_demande]
            ));
        }
    }

    /**
     * Notifier le retour d'une DAPT
     */
    public function notifyDaptReturned(Demande $demande, string $motif = ''): void
    {
        if ($demande->demandeur) {
            $this->sendSafe($demande->demandeur, new WorkflowNotification(
                type: 'dapt_returned',
                title: 'DAPT retournée',
                message: "Votre demande {$demande->numero_demande} a été retournée pour modification." . ($motif ? " Motif: {$motif}" : ''),
                actionUrl: "/demandeur/demandes/{$demande->id}/edit",
                actionText: 'Modifier la demande',
                data: ['demande_id' => $demande->id, 'numero' => $demande->numero_demande, 'motif' => $motif]
            ));
        }
    }

    /**
     * Notifier le refus d'une DAPT
     */
    public function notifyDaptRejected(Demande $demande, string $motif = ''): void
    {
        if ($demande->demandeur) {
            $this->sendSafe($demande->demandeur, new WorkflowNotification(
                type: 'dapt_rejected',
                title: 'DAPT refusée',
                message: "Votre demande {$demande->numero_demande} a été refusée." . ($motif ? " Motif: {$motif}" : ''),
                actionUrl: "/demandeur/demandes/{$demande->id}",
                actionText: 'Voir les détails',
                data: ['demande_id' => $demande->id, 'numero' => $demande->numero_demande, 'motif' => $motif]
            ));
        }
    }

    // ==================== NAPT NOTIFICATIONS ====================

    /**
     * Notifier la soumission d'une NAPT pour vérification
     */
    public function notifyNaptSubmitted(Note $note): void
    {
        $verificateurs = User::role('verificateur')->get();
        
        $this->sendSafe($verificateurs, new WorkflowNotification(
            type: 'napt_submitted',
            title: 'NAPT en attente de vérification',
            message: "La note {$note->numero_note} est en attente de vérification.",
            actionUrl: "/verificateur/notes/{$note->id}",
            actionText: 'Vérifier la note',
            data: ['note_id' => $note->id, 'numero' => $note->numero_note]
        ));

        if ($note->demande && $note->demande->demandeur) {
            $this->sendSafe($note->demande->demandeur, new WorkflowNotification(
                type: 'napt_created',
                title: 'NAPT créée pour votre demande',
                message: "Une note {$note->numero_note} a été créée pour votre demande {$note->demande->numero_demande}.",
                actionUrl: "/demandeur/demandes",
                actionText: 'Voir mes demandes',
                data: ['note_id' => $note->id, 'demande_id' => $note->demande_id]
            ));
        }
    }

    /**
     * Notifier la vérification d'une NAPT
     */
    public function notifyNaptVerified(Note $note): void
    {
        $valideurs = User::role('valideur')->get();
        
        $this->sendSafe($valideurs, new WorkflowNotification(
            type: 'napt_verified',
            title: 'NAPT vérifiée - En attente de validation',
            message: "La note {$note->numero_note} a été vérifiée et attend votre validation.",
            actionUrl: "/valideur/notes/{$note->id}",
            actionText: 'Valider la note',
            data: ['note_id' => $note->id, 'numero' => $note->numero_note]
        ));

        if ($note->etabliPar) {
            $this->sendSafe($note->etabliPar, new WorkflowNotification(
                type: 'napt_verified',
                title: 'Votre NAPT a été vérifiée',
                message: "La note {$note->numero_note} a été vérifiée avec succès.",
                actionUrl: "/desa/notes/{$note->id}",
                actionText: 'Voir la note',
                data: ['note_id' => $note->id, 'numero' => $note->numero_note]
            ));
        }
    }

    /**
     * Notifier la validation d'une NAPT
     */
    public function notifyNaptValidated(Note $note): void
    {
        $operateurs = User::role('operateur')->get();
        
        $this->sendSafe($operateurs, new WorkflowNotification(
            type: 'napt_validated',
            title: 'NAPT validée - Prête pour exécution',
            message: "La note {$note->numero_note} est validée et prête à être exécutée.",
            actionUrl: "/operateur/notes/{$note->id}",
            actionText: 'Exécuter la note',
            data: ['note_id' => $note->id, 'numero' => $note->numero_note]
        ));

        $operateursChef = User::role('operateurchef')->get();
        
        $this->sendSafe($operateursChef, new WorkflowNotification(
            type: 'napt_validated',
            title: 'NAPT validée - Prête pour exécution',
            message: "La note {$note->numero_note} est validée et prête à être exécutée.",
            actionUrl: "/operateurchef/notes/{$note->id}",
            actionText: 'Exécuter la note',
            data: ['note_id' => $note->id, 'numero' => $note->numero_note]
        ));

        if ($note->etabliPar) {
            $this->sendSafe($note->etabliPar, new WorkflowNotification(
                type: 'napt_validated',
                title: 'Votre NAPT a été validée',
                message: "La note {$note->numero_note} a été validée avec succès.",
                actionUrl: "/desa/notes/{$note->id}",
                actionText: 'Voir la note',
                data: ['note_id' => $note->id, 'numero' => $note->numero_note]
            ));
        }

        if ($note->demande && $note->demande->demandeur) {
            $this->sendSafe($note->demande->demandeur, new WorkflowNotification(
                type: 'napt_validated',
                title: 'NAPT validée pour votre demande',
                message: "La note {$note->numero_note} pour votre demande a été validée.",
                actionUrl: "/demandeur/demandes",
                actionText: 'Voir mes demandes',
                data: ['note_id' => $note->id, 'demande_id' => $note->demande_id]
            ));
        }
    }

    /**
     * Notifier le retour d'une NAPT (par vérificateur ou valideur)
     */
    public function notifyNaptReturned(Note $note, string $returnedBy = 'verificateur', string $motif = ''): void
    {
        if ($note->etabliPar) {
            $this->sendSafe($note->etabliPar, new WorkflowNotification(
                type: 'napt_returned',
                title: 'NAPT retournée',
                message: "La note {$note->numero_note} a été retournée par le {$returnedBy}." . ($motif ? " Motif: {$motif}" : ''),
                actionUrl: "/desa/notes/{$note->id}/edit",
                actionText: 'Modifier la note',
                data: ['note_id' => $note->id, 'numero' => $note->numero_note, 'returned_by' => $returnedBy, 'motif' => $motif]
            ));
        }
    }

    /**
     * Notifier l'exécution d'une NAPT
     */
    public function notifyNaptExecuted(Note $note): void
    {
        if ($note->etabliPar) {
            $this->sendSafe($note->etabliPar, new WorkflowNotification(
                type: 'napt_executed',
                title: 'NAPT exécutée',
                message: "La note {$note->numero_note} a été exécutée avec succès.",
                actionUrl: "/desa/notes/{$note->id}",
                actionText: 'Voir la note',
                data: ['note_id' => $note->id, 'numero' => $note->numero_note]
            ));
        }

        if ($note->demande && $note->demande->demandeur) {
            $this->sendSafe($note->demande->demandeur, new WorkflowNotification(
                type: 'napt_executed',
                title: 'Travaux terminés',
                message: "Les travaux de la note {$note->numero_note} pour votre demande ont été exécutés.",
                actionUrl: "/demandeur/demandes",
                actionText: 'Voir mes demandes',
                data: ['note_id' => $note->id, 'demande_id' => $note->demande_id]
            ));
        }
    }

    /**
     * Notifier l'annulation d'une NAPT
     * 
     * @param Note $note La note annulée
     * @param string $cancelledBy Qui a annulé (desa, operateur, operateurchef)
     * @param string $motif Motif d'annulation
     */
    public function notifyNaptCancelled(Note $note, string $cancelledBy = 'desa', string $motif = ''): void
    {
        $cancellerName = $note->annulePar ? $note->annulePar->full_name : 'Un utilisateur';
        $motifText = $motif ? " Motif: {$motif}" : '';

        if ($note->demande && $note->demande->demandeur) {
            $this->sendSafe($note->demande->demandeur, new WorkflowNotification(
                type: 'napt_cancelled',
                title: 'NAPT annulée',
                message: "La note {$note->numero_note} pour votre demande a été annulée par {$cancellerName}.{$motifText}",
                actionUrl: "/demandeur/demandes",
                actionText: 'Voir mes demandes',
                data: [
                    'note_id' => $note->id, 
                    'numero' => $note->numero_note, 
                    'cancelled_by' => $cancelledBy,
                    'motif' => $motif
                ]
            ));
        }

        if (in_array($cancelledBy, ['operateur', 'operateurchef'])) {
            if ($note->etabliPar) {
                $this->sendSafe($note->etabliPar, new WorkflowNotification(
                    type: 'napt_cancelled',
                    title: 'NAPT annulée par l\'opérateur',
                    message: "La note {$note->numero_note} que vous avez créée a été annulée par {$cancellerName}.{$motifText}",
                    actionUrl: "/desa/notes/{$note->id}",
                    actionText: 'Voir la note',
                    data: [
                        'note_id' => $note->id, 
                        'numero' => $note->numero_note, 
                        'cancelled_by' => $cancelledBy,
                        'motif' => $motif
                    ]
                ));
            }

            $desaUsers = User::role('desa')->where('id', '!=', $note->etabli_par)->get();
            
            $this->sendSafe($desaUsers, new WorkflowNotification(
                type: 'napt_cancelled',
                title: 'NAPT annulée par l\'opérateur',
                message: "La note {$note->numero_note} a été annulée par {$cancellerName}.{$motifText}",
                actionUrl: "/desa/notes/{$note->id}",
                actionText: 'Voir la note',
                data: [
                    'note_id' => $note->id, 
                    'numero' => $note->numero_note, 
                    'cancelled_by' => $cancelledBy,
                    'motif' => $motif
                ]
            ));
        }
    }

    // ==================== INTERIM NOTIFICATIONS ====================

    /**
     * Notifier la désignation d'un intérimaire
     */
    public function notifyInterimAssigned(Absence $absence): void
    {
        if ($absence->interimaire) {
            $this->sendSafe($absence->interimaire, new WorkflowNotification(
                type: 'interim_assigned',
                title: 'Vous êtes désigné comme intérimaire',
                message: "Vous avez été désigné comme intérimaire de {$absence->titulaire->full_name} du {$absence->date_debut->format('d/m/Y')} au {$absence->date_fin->format('d/m/Y')}.",
                actionUrl: "/dashboard",
                actionText: 'Voir mon tableau de bord',
                data: [
                    'absence_id' => $absence->id,
                    'titulaire' => $absence->titulaire->full_name,
                    'date_debut' => $absence->date_debut->format('d/m/Y'),
                    'date_fin' => $absence->date_fin->format('d/m/Y'),
                ]
            ));
        }
    }

    /**
     * Notifier la fin d'un intérim
     */
    public function notifyInterimEnded(Absence $absence): void
    {
        if ($absence->interimaire) {
            $this->sendSafe($absence->interimaire, new WorkflowNotification(
                type: 'interim_ended',
                title: 'Fin de votre intérim',
                message: "Votre intérim de {$absence->titulaire->full_name} a pris fin.",
                actionUrl: "/dashboard",
                actionText: 'Voir mon tableau de bord',
                data: ['absence_id' => $absence->id]
            ));
        }
    }

    // ==================== FEEDBACK NOTIFICATIONS ====================

    /**
     * Notifier la réponse à un feedback
     */
    public function notifyFeedbackResponse(Observation $observation, string $response): void
    {
        if ($observation->user) {
            $this->sendSafe($observation->user, new WorkflowNotification(
                type: 'feedback_response',
                title: 'Réponse à votre signalement',
                message: "L'administrateur a répondu à votre signalement: {$response}",
                actionUrl: null,
                actionText: null,
                data: [
                    'observation_id' => $observation->id,
                    'type' => $observation->type,
                    'response' => $response,
                ]
            ));
        }
    }

    /**
     * Notifier les admins d'un nouveau feedback
     */
    public function notifyNewFeedback(Observation $observation): void
    {
        $admins = User::role('admin')->get();
        
        $typeLabel = match($observation->type) {
            'bug' => 'Bug signalé',
            'amelioration' => 'Amélioration suggérée',
            'suggestion' => 'Suggestion',
            default => 'Observation',
        };
        
        $this->sendSafe($admins, new WorkflowNotification(
            type: 'feedback_new',
            title: $typeLabel,
            message: "{$observation->user->full_name} a soumis une observation: " . Str::limit($observation->description, 100),
            actionUrl: "/admin/observations",
            actionText: 'Gérer les observations',
            data: ['observation_id' => $observation->id, 'type' => $observation->type]
        ));
    }
}
