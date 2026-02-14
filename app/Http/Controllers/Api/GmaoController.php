<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SqlServerEquipementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur API pour les données GMAO (SQL Server)
 * 
 * Ce contrôleur gère les appels AJAX pour la récupération des équipements
 * depuis la base de données GMAO (SQL Server) via le SqlServerEquipementController
 */
class GmaoController extends Controller
{
    protected $sqlServerController;

    public function __construct()
    {
        $this->sqlServerController = new SqlServerEquipementController();
    }

    /**
     * Rechercher les lieux d'exécution (postes/lignes parent)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lieuxExecution(Request $request)
    {
        return $this->sqlServerController->getLieuxExecution($request);
    }

    /**
     * Récupérer les équipements enfants d'un parent
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function equipementsEnfants(Request $request)
    {
        $parentCode = $request->input('parent_code');
        
        if (empty($parentCode)) {
            return response()->json([]);
        }

        return $this->sqlServerController->getEquipementsEnfants($parentCode);
    }

    /**
     * Récupérer les détails d'équipements par leurs codes
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function equipementsParCodes(Request $request)
    {
        $codes = $request->input('codes', []);
        
        if (empty($codes)) {
            return response()->json([]);
        }

        if (is_string($codes)) {
            $codes = explode(',', $codes);
        }

        try {
            $results = $this->sqlServerController->getEquipementsByCodes($codes);
            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Erreur GMAO equipementsParCodes: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Récupérer toutes les lignes disponibles
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function allLignes()
    {
        return $this->sqlServerController->getAllLignes();
    }
}
