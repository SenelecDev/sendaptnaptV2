<?php

namespace App\Console\Commands;

use App\Models\Demande;
use App\Models\Note;
use App\Models\User;
use App\Notifications\WorkflowNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendReminders extends Command
{
    protected $signature = 'reminders:send {--dry-run : Ne pas envoyer réellement les notifications}';
    protected $description = 'Envoie des rappels automatiques pour les DAPT/NAPT en attente ou en retard';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $this->info('=== Envoi des rappels automatiques ===');
        
        if ($dryRun) {
            $this->warn('Mode dry-run activé - aucune notification ne sera envoyée');
        }

        $this->remindPendingDapt($dryRun);
        $this->remindPendingNapt($dryRun);
        $this->remindOverdueNapt($dryRun);
        $this->remindUpcomingNapt($dryRun);

        $this->info('=== Rappels terminés ===');
        return Command::SUCCESS;
    }

    /**
     * Rappel pour les DAPT en attente depuis plus de 3 jours
     */
    protected function remindPendingDapt(bool $dryRun)
    {
        $this->info('Vérification des DAPT en attente...');
        
        $pendingDapt = Demande::where('statut', 'créée')
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->with('demandeur')
            ->get();

        $count = 0;
        foreach ($pendingDapt as $demande) {
            // Notifier les DESA
            $desaUsers = User::role('desa')->get();
            
            $this->line("  - DAPT {$demande->numero_demande} en attente depuis {$demande->created_at->diffForHumans()}");
            
            if (!$dryRun) {
                Notification::send($desaUsers, new WorkflowNotification(
                    type: 'reminder',
                    title: 'Rappel : DAPT en attente',
                    message: "La demande {$demande->numero_demande} est en attente de traitement depuis {$demande->created_at->diffForHumans()}.",
                    actionUrl: route('desa.demandes.show', $demande->id),
                    actionText: 'Traiter la demande',
                    data: ['demande_id' => $demande->id]
                ));
                $count++;
            }
        }
        
        $this->info("  → {$pendingDapt->count()} DAPT en attente, {$count} notifications envoyées");
    }

    /**
     * Rappel pour les NAPT en attente de vérification depuis plus de 2 jours
     */
    protected function remindPendingNapt(bool $dryRun)
    {
        $this->info('Vérification des NAPT en attente de vérification...');
        
        $pendingNapt = Note::where('statut', 'en attente de vérification')
            ->where('updated_at', '<', Carbon::now()->subDays(2))
            ->get();

        $count = 0;
        foreach ($pendingNapt as $note) {
            $verificateurs = User::role('verificateur')->get();
            
            $this->line("  - NAPT {$note->numero_note} en attente depuis {$note->updated_at->diffForHumans()}");
            
            if (!$dryRun) {
                Notification::send($verificateurs, new WorkflowNotification(
                    type: 'reminder',
                    title: 'Rappel : NAPT à vérifier',
                    message: "La note {$note->numero_note} attend votre vérification depuis {$note->updated_at->diffForHumans()}.",
                    actionUrl: route('verificateur.notes.show', $note->id),
                    actionText: 'Vérifier la note',
                    data: ['note_id' => $note->id]
                ));
                $count++;
            }
        }
        
        $this->info("  → {$pendingNapt->count()} NAPT en attente, {$count} notifications envoyées");
    }

    /**
     * Alertes pour les NAPT en retard (date_fin dépassée)
     */
    protected function remindOverdueNapt(bool $dryRun)
    {
        $this->info('Vérification des NAPT en retard...');
        
        $overdueNapt = Note::whereDate('dateF', '<', Carbon::today())
            ->whereNotIn('statut', ['executée', 'annulée'])
            ->with(['demande.demandeur', 'etabliPar'])
            ->get();

        $count = 0;
        foreach ($overdueNapt as $note) {
            $daysOverdue = Carbon::parse($note->dateF)->diffInDays(Carbon::today());
            
            $this->line("  - NAPT {$note->numero_note} en retard de {$daysOverdue} jour(s)");
            
            // Notifier l'éditeur DESA et les opérateurs
            $recipients = collect();
            if ($note->etabliPar) {
                $recipients->push($note->etabliPar);
            }
            $recipients = $recipients->merge(User::role(['operateur', 'operateurchef'])->get());
            
            if (!$dryRun && $recipients->isNotEmpty()) {
                Notification::send($recipients->unique('id'), new WorkflowNotification(
                    type: 'urgent',
                    title: 'URGENT : NAPT en retard',
                    message: "La note {$note->numero_note} devait être exécutée le {$note->dateF} (il y a {$daysOverdue} jour(s)).",
                    actionUrl: route('operateur.notes.show', $note->id),
                    actionText: 'Voir la note',
                    data: ['note_id' => $note->id, 'days_overdue' => $daysOverdue]
                ));
                $count++;
            }
        }
        
        $this->info("  → {$overdueNapt->count()} NAPT en retard, {$count} notifications envoyées");
    }

    /**
     * Rappel pour les NAPT prévues demain
     */
    protected function remindUpcomingNapt(bool $dryRun)
    {
        $this->info('Vérification des NAPT prévues demain...');
        
        $upcomingNapt = Note::whereDate('dateD', Carbon::tomorrow())
            ->whereIn('statut', ['validée'])
            ->with(['demande.demandeur', 'etabliPar'])
            ->get();

        $count = 0;
        foreach ($upcomingNapt as $note) {
            $this->line("  - NAPT {$note->numero_note} prévue demain");
            
            // Notifier les opérateurs et le demandeur
            $recipients = collect();
            if ($note->demande && $note->demande->demandeur) {
                $recipients->push($note->demande->demandeur);
            }
            $recipients = $recipients->merge(User::role(['operateur', 'operateurchef'])->get());
            
            if (!$dryRun && $recipients->isNotEmpty()) {
                Notification::send($recipients->unique('id'), new WorkflowNotification(
                    type: 'info',
                    title: 'Rappel : Travaux prévus demain',
                    message: "La note {$note->numero_note} - {$note->demande->designation} est prévue pour demain.",
                    actionUrl: route('operateur.notes.show', $note->id),
                    actionText: 'Voir la note',
                    data: ['note_id' => $note->id]
                ));
                $count++;
            }
        }
        
        $this->info("  → {$upcomingNapt->count()} NAPT prévues demain, {$count} notifications envoyées");
    }
}
