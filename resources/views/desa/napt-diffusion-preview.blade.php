<div class="preview-container">
    <h6 class="text-lg font-semibold text-gray-900 mb-2">Prévisualisation de la diffusion hebdomadaire</h6>
    <p class="text-sm text-gray-500 mb-4">
        Semaine {{ $semaine }}/{{ $annee }} 
        @if($statut)
            - Statut: {{ $statut }}
        @else
            - Toutes les NAPT
        @endif
    </p>
    @if($napts->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 text-left border-b text-gray-700">Semaine</th>
                        <th class="px-2 py-1 text-left border-b text-gray-700">NAPT</th>
                        <th class="px-2 py-1 text-left border-b text-gray-700">Demandeur</th>
                        <th class="px-2 py-1 text-left border-b text-gray-700">Lieu</th>
                        <th class="px-2 py-1 text-left border-b text-gray-700">Installations Consignées</th>
                        <th class="px-2 py-1 text-left border-b text-gray-700">Consistance des Travaux</th>
                        <th class="px-2 py-1 text-left border-b text-gray-700">Début Travaux</th>
                        <th class="px-2 py-1 text-left border-b text-gray-700">Jours</th>
                        <th class="px-2 py-1 text-left border-b text-gray-700">Fin Travaux</th>
                        <th class="px-2 py-1 text-left border-b text-gray-700">Indications</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($napts as $napt)
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-1 border-b">{{ $napt->numero_semaine ?? 'N/A' }}</td>
                            <td class="px-2 py-1 border-b font-medium">{{ $napt->numero_note }}</td>
                            <td class="px-2 py-1 border-b">{{ $napt->demande->demandeur->name ?? 'N/A' }}</td>
                            <td class="px-2 py-1 border-b">
                                @if($napt->demande->mode_saisie === 'manuelle' && $napt->demande->lieu_execution_manuel)
                                    {{ $napt->demande->lieu_execution_manuel }}
                                @else
                                    {{ $napt->demande->lieu_execution ?? 'N/A' }}
                                @endif
                            </td>
                            <td class="px-2 py-1 border-b">
                                @php
                                    $installations = [];
                                    
                                    // Mode manuel - utiliser le champ texte libre
                                    if ($napt->demande->mode_saisie === 'manuelle' || $napt->demande->ouvrage_type === 'manuel') {
                                        if (!empty($napt->demande->ouvrages_consigner_manuel)) {
                                            $installations[] = $napt->demande->ouvrages_consigner_manuel;
                                        }
                                    }
                                    
                                    // Mode GMAO - utiliser ouvrages_consigner_gmao (nouveau champ consolidé)
                                    if (!empty($napt->demande->ouvrages_consigner_gmao)) {
                                        $gmaoData = is_array($napt->demande->ouvrages_consigner_gmao) 
                                            ? $napt->demande->ouvrages_consigner_gmao 
                                            : json_decode($napt->demande->ouvrages_consigner_gmao, true);
                                        
                                        if (is_array($gmaoData)) {
                                            foreach ($gmaoData as $item) {
                                                if (is_array($item)) {
                                                    $desc = $item['description'] ?? $item['EQUIPMENT_DES'] ?? $item['nom'] ?? $item['code'] ?? null;
                                                    if ($desc) $installations[] = $desc;
                                                } elseif (is_string($item)) {
                                                    $installations[] = $item;
                                                }
                                            }
                                        }
                                    }
                                    
                                    // Fallback - anciens champs JSON (lignes_oracle, equipements_oracle)
                                    if (empty($installations)) {
                                        // Lignes
                                        if (!empty($napt->demande->lignes_oracle)) {
                                            $lignesData = json_decode($napt->demande->lignes_oracle, true);
                                            if (is_array($lignesData)) {
                                                foreach ($lignesData as $ligne) {
                                                    $desc = is_array($ligne) ? ($ligne['description'] ?? $ligne['EQUIPMENT_DES'] ?? $ligne['code'] ?? null) : $ligne;
                                                    if ($desc) $installations[] = $desc;
                                                }
                                            }
                                        }
                                        
                                        // Postes
                                        if (!empty($napt->demande->postes_oracle)) {
                                            $postesData = json_decode($napt->demande->postes_oracle, true);
                                            if (is_array($postesData)) {
                                                foreach ($postesData as $poste) {
                                                    $desc = is_array($poste) ? ($poste['description'] ?? $poste['nom'] ?? $poste['code'] ?? null) : $poste;
                                                    if ($desc) $installations[] = $desc;
                                                }
                                            }
                                        }
                                        
                                        // Équipements
                                        if (!empty($napt->demande->equipements_oracle)) {
                                            $equipementsData = json_decode($napt->demande->equipements_oracle, true);
                                            if (is_array($equipementsData)) {
                                                foreach ($equipementsData as $key => $data) {
                                                    if (preg_match('/equipements_consigner_level_\d+/', $key) && is_array($data)) {
                                                        // Prendre le dernier niveau d'équipement
                                                        $lastItem = end($data);
                                                        $desc = is_array($lastItem) ? ($lastItem['description'] ?? $lastItem['EQUIPMENT_DES'] ?? $lastItem['code'] ?? null) : $lastItem;
                                                        if ($desc) $installations[] = $desc;
                                                    } elseif (is_array($data) && isset($data['description'])) {
                                                        $installations[] = $data['description'];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    
                                    // Éviter les doublons
                                    $installations = array_unique(array_filter($installations));
                                @endphp
                                {{ !empty($installations) ? Str::limit(implode(', ', $installations), 100) : 'N/A' }}
                            </td>
                            <td class="px-2 py-1 border-b">{{ Str::limit($napt->demande->designation ?? 'N/A', 50) }}</td>
                            <td class="px-2 py-1 border-b">
                                @if($napt->ddt)
                                    {{ \Carbon\Carbon::parse($napt->ddt)->format('d/m/Y H:i') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-2 py-1 border-b text-center">
                                @if($napt->ddt && $napt->dft)
                                    {{ \Carbon\Carbon::parse($napt->ddt)->diffInDays(\Carbon\Carbon::parse($napt->dft)) + 1 }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-2 py-1 border-b">
                                @if($napt->dft)
                                    {{ \Carbon\Carbon::parse($napt->dft)->format('d/m/Y H:i') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-2 py-1 border-b">{{ Str::limit($napt->renseignementN ?? 'N/A', 50) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm">
            <p class="font-semibold text-blue-800">📊 Résumé : {{ $napts->count() }} NAPT(s) trouvée(s)</p>
            <p class="text-blue-700">Semaine {{ $semaine }}/{{ $annee }} (tous groupes confondus)</p>
            <p class="text-blue-600 text-xs mt-1">Cette liste sera envoyée à tous les groupes sélectionnés.</p>
        </div>
    @else
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg text-center">
            <svg class="w-8 h-8 mx-auto mb-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="font-semibold text-amber-800">Aucune NAPT trouvée</p>
            <p class="text-sm text-amber-600">pour la semaine {{ $semaine }}/{{ $annee }}</p>
        </div>
    @endif
</div>
