<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SqlServerEquipementController extends Controller
{
    /**
     * Connexion SQL Server GMAO
     */
    protected $connection = 'sqlsrv_gmao';

    /**
     * Récupère les lieux d'exécution (Postes et Lignes)
     */
    public function getLieuxExecution(Request $request = null)
    {
        $search = $request ? ($request->input('q') ?? '') : '';
        
        // Utiliser la connexion GMAO réelle
        try {
            $cacheKey = 'lieux_execution_' . md5($search);
            
            $data = Cache::remember($cacheKey, 300, function () use ($search) {
                $query = "
                    SELECT ereq_code, ereq_description, ereq_entity, ereq_function, ereq_category, EREQ_PARENT_EQUIPMENT
                    FROM equipment
                    WHERE ereq_category IN ('P-TRANS', 'P-HTB', 'LIGNE-AER', 'LIGNE-SOUT')
                ";
                
                $params = [];
                if (!empty($search)) {
                    $query .= " AND (ereq_description LIKE ? OR ereq_code LIKE ?)";
                    $params = ["%{$search}%", "%{$search}%"];
                }
                
                $query .= " ORDER BY ereq_category, ereq_description";
                
                $results = DB::connection($this->connection)->select($query, $params);

                $data = [];
                foreach ($results as $row) {
                    $data[] = [
                        'code' => $row->ereq_code,
                        'description' => $row->ereq_description ?? $row->ereq_code,
                        'entity' => $row->ereq_entity ?? null,
                        'function' => $row->ereq_function ?? null,
                        'category' => $row->ereq_category,
                        'parent' => $row->EREQ_PARENT_EQUIPMENT ?? null,
                        'type' => in_array($row->ereq_category, ['LIGNE-AER', 'LIGNE-SOUT']) ? 'ligne' : 'poste'
                    ];
                }
                
                return $data;
            });

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error("SQL Server getLieuxExecution error: " . $e->getMessage());
            
            // Retourner des données de démonstration en cas d'erreur
            return response()->json($this->getDemoLieuxExecution($search));
        }
    }

    /**
     * Récupère les équipements enfants d'un parent donné
     */
    public function getEquipementsEnfants($parentCode)
    {
        if (empty($parentCode)) {
            return response()->json([]);
        }

        // Utiliser la connexion GMAO réelle
        try {
            $cacheKey = 'equipements_enfants_' . md5($parentCode);
            
            $data = Cache::remember($cacheKey, 300, function () use ($parentCode) {
                $results = DB::connection($this->connection)->select("
                    SELECT ereq_code, ereq_description, ereq_entity, ereq_function, ereq_category, EREQ_PARENT_EQUIPMENT
                    FROM equipment
                    WHERE EREQ_PARENT_EQUIPMENT = ?
                    ORDER BY ereq_description
                ", [$parentCode]);

                $data = [];
                foreach ($results as $row) {
                    $data[] = [
                        'code' => $row->ereq_code,
                        'description' => $row->ereq_description ?? $row->ereq_code,
                        'entity' => $row->ereq_entity ?? null,
                        'function' => $row->ereq_function ?? null,
                        'category' => $row->ereq_category,
                        'parent' => $row->EREQ_PARENT_EQUIPMENT ?? null
                    ];
                }
                
                return $data;
            });

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error("SQL Server getEquipementsEnfants error: " . $e->getMessage());
            
            // Retourner des données de démonstration en cas d'erreur
            return response()->json($this->getDemoEquipementsEnfants($parentCode));
        }
    }

    /**
     * Récupère les équipements selon le lieu d'exécution sélectionné
     */
    public function getEquipementsByLieu($lieuCode)
    {
        try {
            return $this->getEquipementsEnfants($lieuCode);
        } catch (\Exception $e) {
            Log::error("SQL Server getEquipementsByLieu error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Récupère les informations complètes d'équipements par leurs codes
     */
    public function getEquipementsByCodes(array $codes)
    {
        if (empty($codes)) {
            return [];
        }

        // Utiliser la connexion GMAO réelle
        try {
            // Créer les placeholders pour la requête IN
            $placeholders = str_repeat('?,', count($codes) - 1) . '?';
            
            $results = DB::connection($this->connection)->select("
                SELECT ereq_code, ereq_description, ereq_entity, ereq_function, ereq_category, EREQ_PARENT_EQUIPMENT
                FROM equipment
                WHERE ereq_code IN ({$placeholders})
                ORDER BY ereq_description
            ", array_values($codes));

            $data = [];
            foreach ($results as $row) {
                $data[] = [
                    'code' => $row->ereq_code,
                    'description' => $row->ereq_description ?? $row->ereq_code,
                    'entity' => $row->ereq_entity ?? null,
                    'function' => $row->ereq_function ?? null,
                    'category' => $row->ereq_category,
                    'parent' => $row->EREQ_PARENT_EQUIPMENT ?? null,
                    'type' => in_array($row->ereq_category, ['LIGNE-AER', 'LIGNE-SOUT']) ? 'ligne' : 'equipement'
                ];
            }

            return $data;

        } catch (\Exception $e) {
            Log::error("SQL Server getEquipementsByCodes error: " . $e->getMessage());
            
            // En cas d'erreur, utiliser les données de démo
            return $this->getDemoEquipementsByCodes($codes);
        }
    }
    
    /**
     * Récupère les informations des équipements par leurs codes depuis les données de démo
     */
    private function getDemoEquipementsByCodes(array $codes): array
    {
        // Construire un index de toutes les données de démo
        $allDemoData = [];
        
        // Lieux d'exécution (Postes et Lignes)
        $lieuxDemo = $this->getDemoLieuxExecution('');
        foreach ($lieuxDemo as $lieu) {
            $allDemoData[$lieu['code']] = $lieu;
        }
        
        // Tous les équipements enfants
        $parentCodes = array_keys($this->getDemoChildrenIndex());
        foreach ($parentCodes as $parentCode) {
            $children = $this->getDemoEquipementsEnfants($parentCode);
            foreach ($children as $child) {
                $allDemoData[$child['code']] = $child;
            }
        }
        
        // Récupérer les données pour les codes demandés
        $result = [];
        foreach ($codes as $code) {
            if (isset($allDemoData[$code])) {
                $result[] = $allDemoData[$code];
            } else {
                // Fallback si le code n'est pas dans les données de démo
                $result[] = ['code' => $code, 'description' => $code];
            }
        }
        
        return $result;
    }
    
    /**
     * Retourne l'index des codes parents qui ont des enfants
     */
    private function getDemoChildrenIndex(): array
    {
        return [
            // Postes
            'POSTE-DK001' => true,
            'POSTE-DK002' => true,
            'POSTE-TH001' => true,
            'POSTE-KL001' => true,
            'POSTE-STL01' => true,
            'POSTE-ZG001' => true,
            // Sous-équipements postes
            'DK001-TRF1' => true,
            'DK001-TRF1-DJ-HT' => true,
            'DK001-TRF1-DJ-HT-MEC' => true,
            'DK001-TRF1-DJ-HT-MEC-MOT' => true,
            'DK001-TRF1-DJ-HT-MEC-MOT-ROT' => true,
            'DK001-TRF2' => true,
            'KL001-TRF1' => true,
            'STL01-TRF1' => true,
            'ZG001-TRF1' => true,
            // Lignes
            'LIGNE-90-001' => true,
            'LIGNE-90-002' => true,
            'LIGNE-90-003' => true,
            'LIGNE-30-001' => true,
            'LIGNE-30-002' => true,
            'LIGNE-SOUT-01' => true,
        ];
    }

    /**
     * Récupère toutes les lignes disponibles
     */
    public function getAllLignes()
    {
        // Utiliser la connexion GMAO réelle
        try {
            $cacheKey = 'all_lignes';
            
            $data = Cache::remember($cacheKey, 600, function () {
                $results = DB::connection($this->connection)->select("
                    SELECT ereq_code, ereq_description, ereq_entity, ereq_function, ereq_category, EREQ_PARENT_EQUIPMENT
                    FROM equipment
                    WHERE ereq_category IN ('LIGNE-AER','LIGNE-SOUT')
                    ORDER BY ereq_description
                ");

                $data = [];
                foreach ($results as $row) {
                    $data[] = [
                        'code' => $row->ereq_code,
                        'description' => $row->ereq_description ?? $row->ereq_code,
                        'entity' => $row->ereq_entity ?? null,
                        'function' => $row->ereq_function ?? null,
                        'category' => $row->ereq_category,
                        'parent' => $row->EREQ_PARENT_EQUIPMENT ?? null,
                        'type' => 'ligne'
                    ];
                }
                
                return $data;
            });

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error("SQL Server getAllLignes error: " . $e->getMessage());
            return response()->json($this->getDemoAllLignes());
        }
    }

    /**
     * Données de démonstration pour toutes les lignes
     */
    private function getDemoAllLignes(): array
    {
        return [
            ['code' => 'LIGNE-90-001', 'description' => 'Ligne 90kV Dakar-Thiès', 'type' => 'ligne', 'category' => 'LIGNE-AER'],
            ['code' => 'LIGNE-90-002', 'description' => 'Ligne 90kV Thiès-Kaolack', 'type' => 'ligne', 'category' => 'LIGNE-AER'],
            ['code' => 'LIGNE-90-003', 'description' => 'Ligne 90kV Kaolack-Tambacounda', 'type' => 'ligne', 'category' => 'LIGNE-AER'],
            ['code' => 'LIGNE-30-001', 'description' => 'Ligne 30kV Distribution Dakar Nord', 'type' => 'ligne', 'category' => 'LIGNE-AER'],
            ['code' => 'LIGNE-30-002', 'description' => 'Ligne 30kV Distribution Dakar Sud', 'type' => 'ligne', 'category' => 'LIGNE-AER'],
            ['code' => 'LIGNE-SOUT-01', 'description' => 'Câble souterrain 30kV Plateau', 'type' => 'ligne', 'category' => 'LIGNE-SOUT'],
        ];
    }

    /**
     * Données de démonstration pour les lieux d'exécution
     */
    private function getDemoLieuxExecution(string $search = ''): array
    {
        $demoData = [
            ['code' => 'POSTE-DK001', 'description' => 'Poste DAKAR 1 - 90kV', 'type' => 'poste', 'category' => 'P-TRANS'],
            ['code' => 'POSTE-DK002', 'description' => 'Poste DAKAR 2 - 30kV', 'type' => 'poste', 'category' => 'P-TRANS'],
            ['code' => 'POSTE-TH001', 'description' => 'Poste THIES Central - 90kV', 'type' => 'poste', 'category' => 'P-TRANS'],
            ['code' => 'POSTE-KL001', 'description' => 'Poste KAOLACK - 30kV', 'type' => 'poste', 'category' => 'P-TRANS'],
            ['code' => 'POSTE-STL01', 'description' => 'Poste SAINT-LOUIS - 90kV', 'type' => 'poste', 'category' => 'P-TRANS'],
            ['code' => 'POSTE-ZG001', 'description' => 'Poste ZIGUINCHOR - 30kV', 'type' => 'poste', 'category' => 'P-TRANS'],
            ['code' => 'LIGNE-90-001', 'description' => 'Ligne 90kV Dakar-Thiès', 'type' => 'ligne', 'category' => 'LIGNE-AER'],
            ['code' => 'LIGNE-90-002', 'description' => 'Ligne 90kV Thiès-Kaolack', 'type' => 'ligne', 'category' => 'LIGNE-AER'],
            ['code' => 'LIGNE-90-003', 'description' => 'Ligne 90kV Kaolack-Tambacounda', 'type' => 'ligne', 'category' => 'LIGNE-AER'],
            ['code' => 'LIGNE-30-001', 'description' => 'Ligne 30kV Distribution Dakar Nord', 'type' => 'ligne', 'category' => 'LIGNE-AER'],
            ['code' => 'LIGNE-30-002', 'description' => 'Ligne 30kV Distribution Dakar Sud', 'type' => 'ligne', 'category' => 'LIGNE-AER'],
            ['code' => 'LIGNE-SOUT-01', 'description' => 'Câble souterrain 30kV Plateau', 'type' => 'ligne', 'category' => 'LIGNE-SOUT'],
        ];

        if (empty($search)) {
            return $demoData;
        }

        return array_values(array_filter($demoData, function ($item) use ($search) {
            return stripos($item['description'], $search) !== false || 
                   stripos($item['code'], $search) !== false;
        }));
    }

    /**
     * Données de démonstration pour les équipements enfants
     */
    private function getDemoEquipementsEnfants(string $parentCode): array
    {
        $demoChildren = [
            'POSTE-DK001' => [
                ['code' => 'DK001-TRF1', 'description' => 'Transformateur T1 - 90/30kV - 40MVA', 'category' => 'TRANSFO'],
                ['code' => 'DK001-TRF2', 'description' => 'Transformateur T2 - 90/30kV - 40MVA', 'category' => 'TRANSFO'],
                ['code' => 'DK001-JB90-1', 'description' => 'Jeu de barres 90kV N°1', 'category' => 'JDB'],
                ['code' => 'DK001-JB90-2', 'description' => 'Jeu de barres 90kV N°2', 'category' => 'JDB'],
                ['code' => 'DK001-DEP-TH', 'description' => 'Départ Thiès 90kV', 'category' => 'DEPART'],
                ['code' => 'DK001-ARR-CB', 'description' => 'Arrivée Cap des Biches', 'category' => 'ARRIVEE'],
            ],
            'POSTE-DK002' => [
                ['code' => 'DK002-TRF1', 'description' => 'Transformateur T1 - 30/6.6kV - 20MVA', 'category' => 'TRANSFO'],
                ['code' => 'DK002-JB30-1', 'description' => 'Jeu de barres 30kV', 'category' => 'JDB'],
                ['code' => 'DK002-DEP-01', 'description' => 'Départ HTA Fann', 'category' => 'DEPART'],
                ['code' => 'DK002-DEP-02', 'description' => 'Départ HTA Médina', 'category' => 'DEPART'],
                ['code' => 'DK002-DEP-03', 'description' => 'Départ HTA Plateau', 'category' => 'DEPART'],
            ],
            'POSTE-TH001' => [
                ['code' => 'TH001-TRF1', 'description' => 'Transformateur T1 - 90/30kV - 60MVA', 'category' => 'TRANSFO'],
                ['code' => 'TH001-TRF2', 'description' => 'Transformateur T2 - 90/30kV - 60MVA', 'category' => 'TRANSFO'],
                ['code' => 'TH001-JB90', 'description' => 'Jeu de barres 90kV Principal', 'category' => 'JDB'],
                ['code' => 'TH001-ARR-DK', 'description' => 'Arrivée Dakar 90kV', 'category' => 'ARRIVEE'],
                ['code' => 'TH001-DEP-KL', 'description' => 'Départ Kaolack 90kV', 'category' => 'DEPART'],
            ],
            'DK001-TRF1' => [
                ['code' => 'DK001-TRF1-DJ-HT', 'description' => 'Disjoncteur HT Transfo T1', 'category' => 'DISJ'],
                ['code' => 'DK001-TRF1-DJ-MT', 'description' => 'Disjoncteur MT Transfo T1', 'category' => 'DISJ'],
                ['code' => 'DK001-TRF1-SECT-HT', 'description' => 'Sectionneur HT Transfo T1', 'category' => 'SECT'],
                ['code' => 'DK001-TRF1-SECT-MT', 'description' => 'Sectionneur MT Transfo T1', 'category' => 'SECT'],
                ['code' => 'DK001-TRF1-TC', 'description' => 'TC Protection Transfo T1', 'category' => 'TC'],
                ['code' => 'DK001-TRF1-TP', 'description' => 'TP Mesure Transfo T1', 'category' => 'TP'],
            ],
            // Niveau 3 - Disjoncteur HT
            'DK001-TRF1-DJ-HT' => [
                ['code' => 'DK001-TRF1-DJ-HT-MEC', 'description' => 'Mécanisme de commande', 'category' => 'MECANISME'],
                ['code' => 'DK001-TRF1-DJ-HT-CUVE', 'description' => 'Cuve SF6', 'category' => 'CUVE'],
                ['code' => 'DK001-TRF1-DJ-HT-CONT', 'description' => 'Contacts principaux', 'category' => 'CONTACTS'],
                ['code' => 'DK001-TRF1-DJ-HT-PROT', 'description' => 'Relais de protection', 'category' => 'PROTECTION'],
            ],
            // Niveau 4 - Mécanisme de commande
            'DK001-TRF1-DJ-HT-MEC' => [
                ['code' => 'DK001-TRF1-DJ-HT-MEC-MOT', 'description' => 'Moteur d\'armement', 'category' => 'MOTEUR'],
                ['code' => 'DK001-TRF1-DJ-HT-MEC-RES', 'description' => 'Ressorts d\'accumulation', 'category' => 'RESSORT'],
                ['code' => 'DK001-TRF1-DJ-HT-MEC-VER', 'description' => 'Verrouillage mécanique', 'category' => 'VERROUILLAGE'],
                ['code' => 'DK001-TRF1-DJ-HT-MEC-BOB', 'description' => 'Bobines d\'ouverture/fermeture', 'category' => 'BOBINE'],
            ],
            // Niveau 5 - Moteur d'armement
            'DK001-TRF1-DJ-HT-MEC-MOT' => [
                ['code' => 'DK001-TRF1-DJ-HT-MEC-MOT-ROT', 'description' => 'Rotor', 'category' => 'ROTOR'],
                ['code' => 'DK001-TRF1-DJ-HT-MEC-MOT-STA', 'description' => 'Stator', 'category' => 'STATOR'],
                ['code' => 'DK001-TRF1-DJ-HT-MEC-MOT-RED', 'description' => 'Réducteur', 'category' => 'REDUCTEUR'],
                ['code' => 'DK001-TRF1-DJ-HT-MEC-MOT-FDC', 'description' => 'Fin de course', 'category' => 'CAPTEUR'],
            ],
            // Niveau 6 - Rotor (niveau final)
            'DK001-TRF1-DJ-HT-MEC-MOT-ROT' => [
                ['code' => 'DK001-TRF1-DJ-HT-MEC-MOT-ROT-AXE', 'description' => 'Axe principal', 'category' => 'AXE'],
                ['code' => 'DK001-TRF1-DJ-HT-MEC-MOT-ROT-ROUL', 'description' => 'Roulements', 'category' => 'ROULEMENT'],
                ['code' => 'DK001-TRF1-DJ-HT-MEC-MOT-ROT-VENT', 'description' => 'Ventilateur', 'category' => 'VENTILATEUR'],
            ],
            'DK001-TRF2' => [
                ['code' => 'DK001-TRF2-DJ-HT', 'description' => 'Disjoncteur HT Transfo T2', 'category' => 'DISJ'],
                ['code' => 'DK001-TRF2-DJ-MT', 'description' => 'Disjoncteur MT Transfo T2', 'category' => 'DISJ'],
                ['code' => 'DK001-TRF2-SECT-HT', 'description' => 'Sectionneur HT Transfo T2', 'category' => 'SECT'],
                ['code' => 'DK001-TRF2-SECT-MT', 'description' => 'Sectionneur MT Transfo T2', 'category' => 'SECT'],
            ],
            // Postes supplémentaires
            'POSTE-KL001' => [
                ['code' => 'KL001-TRF1', 'description' => 'Transformateur T1 - 30/6.6kV - 25MVA', 'category' => 'TRANSFO'],
                ['code' => 'KL001-JB30', 'description' => 'Jeu de barres 30kV', 'category' => 'JDB'],
                ['code' => 'KL001-DEP-01', 'description' => 'Départ HTA Zone Industrielle', 'category' => 'DEPART'],
                ['code' => 'KL001-DEP-02', 'description' => 'Départ HTA Centre-Ville', 'category' => 'DEPART'],
                ['code' => 'KL001-ARR-TH', 'description' => 'Arrivée Thiès 90kV', 'category' => 'ARRIVEE'],
            ],
            'POSTE-STL01' => [
                ['code' => 'STL01-TRF1', 'description' => 'Transformateur T1 - 90/30kV - 40MVA', 'category' => 'TRANSFO'],
                ['code' => 'STL01-TRF2', 'description' => 'Transformateur T2 - 90/30kV - 40MVA', 'category' => 'TRANSFO'],
                ['code' => 'STL01-JB90', 'description' => 'Jeu de barres 90kV', 'category' => 'JDB'],
                ['code' => 'STL01-JB30', 'description' => 'Jeu de barres 30kV', 'category' => 'JDB'],
                ['code' => 'STL01-DEP-01', 'description' => 'Départ HTA Gandiol', 'category' => 'DEPART'],
                ['code' => 'STL01-DEP-02', 'description' => 'Départ HTA Langue de Barbarie', 'category' => 'DEPART'],
            ],
            'POSTE-ZG001' => [
                ['code' => 'ZG001-TRF1', 'description' => 'Transformateur T1 - 30/6.6kV - 15MVA', 'category' => 'TRANSFO'],
                ['code' => 'ZG001-JB30', 'description' => 'Jeu de barres 30kV', 'category' => 'JDB'],
                ['code' => 'ZG001-DEP-01', 'description' => 'Départ HTA Boucotte', 'category' => 'DEPART'],
                ['code' => 'ZG001-DEP-02', 'description' => 'Départ HTA Océan', 'category' => 'DEPART'],
                ['code' => 'ZG001-GE-01', 'description' => 'Groupe Électrogène 1', 'category' => 'GE'],
            ],
            // Sous-niveaux pour les transformateurs des autres postes
            'KL001-TRF1' => [
                ['code' => 'KL001-TRF1-DJ-HT', 'description' => 'Disjoncteur HT', 'category' => 'DISJ'],
                ['code' => 'KL001-TRF1-DJ-MT', 'description' => 'Disjoncteur MT', 'category' => 'DISJ'],
                ['code' => 'KL001-TRF1-SECT', 'description' => 'Sectionneur', 'category' => 'SECT'],
            ],
            'STL01-TRF1' => [
                ['code' => 'STL01-TRF1-DJ-HT', 'description' => 'Disjoncteur HT', 'category' => 'DISJ'],
                ['code' => 'STL01-TRF1-DJ-MT', 'description' => 'Disjoncteur MT', 'category' => 'DISJ'],
                ['code' => 'STL01-TRF1-SECT', 'description' => 'Sectionneur', 'category' => 'SECT'],
                ['code' => 'STL01-TRF1-TC', 'description' => 'TC Protection', 'category' => 'TC'],
            ],
            'ZG001-TRF1' => [
                ['code' => 'ZG001-TRF1-DJ', 'description' => 'Disjoncteur', 'category' => 'DISJ'],
                ['code' => 'ZG001-TRF1-SECT', 'description' => 'Sectionneur', 'category' => 'SECT'],
            ],
            'LIGNE-90-001' => [
                ['code' => 'L90-001-PYL-01', 'description' => 'Pylône N°1 - Départ Dakar', 'category' => 'PYLONE'],
                ['code' => 'L90-001-PYL-15', 'description' => 'Pylône N°15 - Ancrage', 'category' => 'PYLONE'],
                ['code' => 'L90-001-PYL-30', 'description' => 'Pylône N°30 - Arrivée Thiès', 'category' => 'PYLONE'],
                ['code' => 'L90-001-COND-A', 'description' => 'Conducteur Phase A', 'category' => 'COND'],
                ['code' => 'L90-001-COND-B', 'description' => 'Conducteur Phase B', 'category' => 'COND'],
                ['code' => 'L90-001-COND-C', 'description' => 'Conducteur Phase C', 'category' => 'COND'],
                ['code' => 'L90-001-CABLE-GARDE', 'description' => 'Câble de garde', 'category' => 'COND'],
            ],
            'LIGNE-90-002' => [
                ['code' => 'L90-002-PYL-01', 'description' => 'Pylône N°1 - Départ Thiès', 'category' => 'PYLONE'],
                ['code' => 'L90-002-PYL-20', 'description' => 'Pylône N°20 - Ancrage', 'category' => 'PYLONE'],
                ['code' => 'L90-002-PYL-40', 'description' => 'Pylône N°40 - Arrivée Kaolack', 'category' => 'PYLONE'],
                ['code' => 'L90-002-COND-A', 'description' => 'Conducteur Phase A', 'category' => 'COND'],
                ['code' => 'L90-002-COND-B', 'description' => 'Conducteur Phase B', 'category' => 'COND'],
                ['code' => 'L90-002-COND-C', 'description' => 'Conducteur Phase C', 'category' => 'COND'],
            ],
            'LIGNE-90-003' => [
                ['code' => 'L90-003-PYL-01', 'description' => 'Pylône N°1 - Départ Kaolack', 'category' => 'PYLONE'],
                ['code' => 'L90-003-PYL-25', 'description' => 'Pylône N°25 - Ancrage', 'category' => 'PYLONE'],
                ['code' => 'L90-003-PYL-50', 'description' => 'Pylône N°50 - Arrivée Tambacounda', 'category' => 'PYLONE'],
                ['code' => 'L90-003-COND-A', 'description' => 'Conducteur Phase A', 'category' => 'COND'],
                ['code' => 'L90-003-COND-B', 'description' => 'Conducteur Phase B', 'category' => 'COND'],
                ['code' => 'L90-003-COND-C', 'description' => 'Conducteur Phase C', 'category' => 'COND'],
            ],
            'LIGNE-30-001' => [
                ['code' => 'L30-001-PYL-01', 'description' => 'Pylône N°1', 'category' => 'PYLONE'],
                ['code' => 'L30-001-PYL-10', 'description' => 'Pylône N°10 - Dérivation', 'category' => 'PYLONE'],
                ['code' => 'L30-001-COND-A', 'description' => 'Conducteur Phase A', 'category' => 'COND'],
                ['code' => 'L30-001-COND-B', 'description' => 'Conducteur Phase B', 'category' => 'COND'],
                ['code' => 'L30-001-COND-C', 'description' => 'Conducteur Phase C', 'category' => 'COND'],
            ],
            'LIGNE-30-002' => [
                ['code' => 'L30-002-PYL-01', 'description' => 'Pylône N°1 - Départ', 'category' => 'PYLONE'],
                ['code' => 'L30-002-PYL-15', 'description' => 'Pylône N°15 - Ancrage', 'category' => 'PYLONE'],
                ['code' => 'L30-002-COND-A', 'description' => 'Conducteur Phase A', 'category' => 'COND'],
                ['code' => 'L30-002-COND-B', 'description' => 'Conducteur Phase B', 'category' => 'COND'],
                ['code' => 'L30-002-COND-C', 'description' => 'Conducteur Phase C', 'category' => 'COND'],
            ],
            'LIGNE-SOUT-01' => [
                ['code' => 'LSOUT-01-JONC-01', 'description' => 'Jonction N°1 - Plateau', 'category' => 'JONCTION'],
                ['code' => 'LSOUT-01-JONC-02', 'description' => 'Jonction N°2 - Médina', 'category' => 'JONCTION'],
                ['code' => 'LSOUT-01-CABLE-A', 'description' => 'Câble Phase A', 'category' => 'CABLE'],
                ['code' => 'LSOUT-01-CABLE-B', 'description' => 'Câble Phase B', 'category' => 'CABLE'],
                ['code' => 'LSOUT-01-CABLE-C', 'description' => 'Câble Phase C', 'category' => 'CABLE'],
            ],
        ];

        return $demoChildren[$parentCode] ?? [];
    }
}
