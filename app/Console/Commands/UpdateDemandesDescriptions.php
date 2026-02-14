<?php

namespace App\Console\Commands;

use App\Models\Demande;
use Illuminate\Console\Command;
use App\Http\Controllers\SqlServerEquipementController;

class UpdateDemandesDescriptions extends Command
{
    protected $signature = 'demandes:update-descriptions';
    protected $description = 'Met à jour les descriptions des équipements GMAO dans toutes les demandes existantes';

    public function handle()
    {
        $this->info('Mise à jour des descriptions des équipements...');
        
        $sqlServerController = new SqlServerEquipementController();
        
        $demandes = Demande::whereNotNull('equipements_oracle')
            ->orWhereNotNull('equipements_installer_oracle')
            ->orWhereNotNull('lignes_oracle')
            ->orWhereNotNull('lignes_installer_oracle')
            ->get();
        
        $this->info("Nombre de demandes à traiter : " . $demandes->count());
        
        $bar = $this->output->createProgressBar($demandes->count());
        $bar->start();
        
        foreach ($demandes as $demande) {
            $updated = false;
            
            // Mettre à jour les équipements à consigner
            if ($demande->equipements_oracle) {
                $equipementsData = json_decode($demande->equipements_oracle, true);
                if (is_array($equipementsData)) {
                    $updatedEquipements = $this->updateEquipementsDescriptions($equipementsData, $sqlServerController);
                    $demande->equipements_oracle = json_encode($updatedEquipements);
                    $updated = true;
                }
            }
            
            // Mettre à jour les équipements à installer
            if ($demande->equipements_installer_oracle) {
                $equipementsData = json_decode($demande->equipements_installer_oracle, true);
                if (is_array($equipementsData)) {
                    $updatedEquipements = $this->updateEquipementsDescriptions($equipementsData, $sqlServerController);
                    $demande->equipements_installer_oracle = json_encode($updatedEquipements);
                    $updated = true;
                }
            }
            
            // Mettre à jour les lignes à consigner
            if ($demande->lignes_oracle) {
                $lignesData = json_decode($demande->lignes_oracle, true);
                if (is_array($lignesData)) {
                    $codes = array_map(function($ligne) {
                        return is_array($ligne) ? ($ligne['code'] ?? $ligne) : $ligne;
                    }, $lignesData);
                    $updatedLignes = $sqlServerController->getEquipementsByCodes($codes);
                    $demande->lignes_oracle = json_encode($updatedLignes);
                    $updated = true;
                }
            }
            
            // Mettre à jour les lignes à installer
            if ($demande->lignes_installer_oracle) {
                $lignesData = json_decode($demande->lignes_installer_oracle, true);
                if (is_array($lignesData)) {
                    $codes = array_map(function($ligne) {
                        return is_array($ligne) ? ($ligne['code'] ?? $ligne) : $ligne;
                    }, $lignesData);
                    $updatedLignes = $sqlServerController->getEquipementsByCodes($codes);
                    $demande->lignes_installer_oracle = json_encode($updatedLignes);
                    $updated = true;
                }
            }
            
            if ($updated) {
                $demande->save();
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Mise à jour terminée !');
        
        return Command::SUCCESS;
    }
    
    private function updateEquipementsDescriptions(array $equipementsData, SqlServerEquipementController $controller): array
    {
        $updatedData = [];
        
        foreach ($equipementsData as $levelKey => $levelEquipements) {
            if (is_array($levelEquipements)) {
                // Extraire les codes
                $codes = array_map(function($equipement) {
                    return is_array($equipement) ? ($equipement['code'] ?? $equipement) : $equipement;
                }, $levelEquipements);
                
                // Récupérer les nouvelles données avec descriptions
                $updatedEquipements = $controller->getEquipementsByCodes($codes);
                $updatedData[$levelKey] = $updatedEquipements;
            }
        }
        
        return $updatedData;
    }
}
