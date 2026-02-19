<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateV1Data extends Command
{
    protected $signature = 'migrate:v1-data
                            {--dry-run : Afficher le rapport sans insérer}
                            {--connection=mysql_v1 : Nom de la connexion V1}
                            {--demandes-only : Migrer uniquement les demandes}
                            {--notes-only : Migrer uniquement les notes}
                            {--charges-travaux-only : Migrer uniquement les charges de travaux}
                            {--signatures-only : Récupérer les signatures V1 (sans écraser les existantes)}
                            {--truncate : Vider les tables V2 avant insertion}
                            {--force : Ne pas demander de confirmation}';

    protected $description = 'Migre les demandes et notes de la base V1 vers la V2';

    private string $v1Connection;
    private array $userMapById = [];
    private array $demandeIdMap = [];
    private array $noteIdMap = [];
    private array $chargeConsIdMap = [];
    private array $correspondantIdMap = [];
    private array $serviceDestIdMap = [];
    private array $chargeTravauxIdMap = [];

    public function handle(): int
    {
        $this->v1Connection = $this->option('connection');

        // 1. Test connexion V1
        $this->info('=== Migration V1 → V2 ===');
        $this->newLine();

        try {
            DB::connection($this->v1Connection)->getPdo();
            $this->info('✓ Connexion V1 établie.');
        } catch (\Exception $e) {
            $this->error('✗ Impossible de se connecter à la V1: ' . $e->getMessage());
            $this->warn('Configurez V1_DB_HOST, V1_DB_DATABASE, etc. dans .env');
            return self::FAILURE;
        }

        // 2. Construire le mapping des users (V1 id → V2 id) par matricule
        $this->buildUserMap();

        // Mode: uniquement signatures
        if ($this->option('signatures-only')) {
            $this->newLine();
            $this->info('=== SIGNATURES UTILISATEURS ===');
            $this->migrateSignatures();

            $this->newLine();
            $this->info('=== Migration terminée ===');
            return self::SUCCESS;
        }

        // Mode: uniquement charges de travaux
        if ($this->option('charges-travaux-only')) {
            if ($this->option('truncate')) {
                $this->disableForeignKeys();
                DB::table('charges_travaux')->truncate();
                $this->enableForeignKeys();
                $this->info('✓ Table charges_travaux vidée.');
            }

            $this->newLine();
            $this->info('Reconstruction du mapping demandes V1 → V2...');
            $this->rebuildDemandeIdMap();

            $this->newLine();
            $this->info('=== CHARGES DE TRAVAUX ===');
            $this->migrateChargesTravaux();

            $this->newLine();
            $this->info('=== Migration terminée ===');
            return self::SUCCESS;
        }

        // 3. Analyser les schémas
        if (!$this->option('notes-only')) {
            $this->newLine();
            $this->info('=== DEMANDES ===');
            $this->migrateDemandes();
        }

        if (!$this->option('demandes-only')) {
            $this->newLine();
            $this->info('=== CONTACTS (charges consignation, correspondants, services) ===');
            $this->migrateContacts();

            $this->newLine();
            $this->info('=== NOTES ===');
            $this->migrateNotes();

            $this->newLine();
            $this->info('=== TABLES PIVOT (note ↔ contacts) ===');
            $this->migratePivotTables();
        }

        // Charges de travaux (à la fin car dépend des demandes migrées)
        if (!$this->option('notes-only') && !$this->option('demandes-only')) {
            $this->newLine();
            $this->info('=== CHARGES DE TRAVAUX ===');
            $this->migrateChargesTravaux();
        }

        $this->newLine();
        $this->info('=== Migration terminée ===');
        return self::SUCCESS;
    }

    private function buildUserMap(): void
    {
        $this->info('Construction du mapping utilisateurs V1 → V2 (par matricule)...');

        $v1Users = DB::connection($this->v1Connection)
            ->table('users')
            ->select('id', 'user_matricule', 'name', 'email')
            ->get();

        $v2Users = DB::table('users')
            ->select('id', 'matricule', 'name', 'email')
            ->get();

        $v2ByMatricule = $v2Users->keyBy('matricule');
        $v2ByEmail = $v2Users->keyBy('email');

        $mapped = 0;
        $unmapped = 0;
        $unmappedList = [];

        foreach ($v1Users as $v1User) {
            $matched = null;

            if ($v1User->user_matricule && isset($v2ByMatricule[$v1User->user_matricule])) {
                $matched = $v2ByMatricule[$v1User->user_matricule];
            }

            if (!$matched && $v1User->email && isset($v2ByEmail[$v1User->email])) {
                $matched = $v2ByEmail[$v1User->email];
            }

            if ($matched) {
                $this->userMapById[$v1User->id] = $matched->id;
                $mapped++;
            } else {
                $unmapped++;
                $unmappedList[] = "#{$v1User->id} {$v1User->name} ({$v1User->user_matricule})";
            }
        }

        $this->info("  ✓ {$mapped} utilisateurs mappés, {$unmapped} non trouvés en V2.");

        if ($unmapped > 0) {
            $this->warn("  Les FK vers des utilisateurs non mappés seront mises à NULL.");
            if ($this->getOutput()->isVerbose() || $unmapped <= 10) {
                foreach ($unmappedList as $u) {
                    $this->warn("    - {$u}");
                }
            }
        }
    }

    private function mapUserId(?int $v1UserId): ?int
    {
        if ($v1UserId === null) {
            return null;
        }
        return $this->userMapById[$v1UserId] ?? null;
    }

    private function getV1Columns(string $table): array
    {
        return DB::connection($this->v1Connection)
            ->getSchemaBuilder()
            ->getColumnListing($table);
    }

    private function getV2Columns(string $table): array
    {
        return Schema::getColumnListing($table);
    }

    private function migrateDemandes(): void
    {
        if (!$this->v1TableExists('demandes')) {
            $this->error('Table "demandes" introuvable en V1.');
            return;
        }

        $v1Cols = $this->getV1Columns('demandes');
        $v2Cols = $this->getV2Columns('demandes');

        $this->showSchemaComparison('demandes', $v1Cols, $v2Cols);

        $commonCols = array_intersect($v1Cols, $v2Cols);
        // Exclure 'id' et timestamps auto-gérés par le mapping
        $colsToSkip = ['id'];
        $commonCols = array_diff($commonCols, $colsToSkip);

        // Colonnes FK qui pointent vers users — on les mappe
        $userFkCols = ['demandeur_id', 'charge_travaux_id', 'traite_id', 'user_id'];

        $v1Demandes = DB::connection($this->v1Connection)
            ->table('demandes')
            ->orderBy('id')
            ->get();

        $this->info("  {$v1Demandes->count()} demandes trouvées en V1.");

        if ($this->option('dry-run')) {
            $this->warn('  [DRY-RUN] Aucune insertion effectuée.');
            return;
        }

        if (!$this->option('force') && !$this->confirm("Insérer {$v1Demandes->count()} demandes dans la V2 ?")) {
            $this->warn('  Annulé.');
            return;
        }

        if ($this->option('truncate')) {
            $this->warn('  Vidage de toutes les tables liées...');
            $this->disableForeignKeys();
            DB::table('note_histories')->truncate();
            DB::table('note_service')->truncate();
            DB::table('note_correspondant')->truncate();
            DB::table('note_charge_consignation')->truncate();
            DB::table('notes')->truncate();
            DB::table('demande_histories')->truncate();
            DB::table('charges_travaux')->truncate();
            DB::table('demandes')->truncate();
            DB::table('charges_cons')->truncate();
            DB::table('correspondants')->truncate();
            DB::table('services_dest')->truncate();
            $this->enableForeignKeys();
        }

        $inserted = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($v1Demandes->count());

        foreach ($v1Demandes as $v1Row) {
            $data = [];
            $v1Data = (array) $v1Row;

            foreach ($commonCols as $col) {
                if (in_array($col, $userFkCols)) {
                    $data[$col] = $this->mapUserId($v1Data[$col] ?? null);
                } else {
                    $data[$col] = $v1Data[$col] ?? null;
                }
            }

            // demandeur_id est obligatoire (NOT NULL + FK) — si non mappé, skip
            if (in_array('demandeur_id', $commonCols) && empty($data['demandeur_id'])) {
                $fallbackId = DB::table('users')->value('id');
                if ($fallbackId) {
                    $data['demandeur_id'] = $fallbackId;
                } else {
                    $this->newLine();
                    $this->warn("  Skip demande V1 #{$v1Row->id}: demandeur_id non mappable et aucun user en V2.");
                    $errors++;
                    $bar->advance();
                    continue;
                }
            }

            // Adapter les enums si les valeurs ont changé entre V1 et V2
            if (isset($data['statut'])) {
                $data['statut'] = $this->mapDemandeStatut($data['statut']);
            }
            if (isset($data['ouvrage_type'])) {
                $data['ouvrage_type'] = $this->mapOuvrageType($data['ouvrage_type']);
            }
            if (isset($data['mode_saisie'])) {
                $data['mode_saisie'] = $this->mapModeSaisie($data['mode_saisie']);
            }
            if (isset($data['etape'])) {
                $data['etape'] = $this->mapEtape($data['etape']);
            }

            // Colonnes devenues boolean en V2 mais qui étaient time/string en V1
            if (array_key_exists('dmra', $data)) {
                $data['dmra'] = $this->toBool($data['dmra']);
            }
            if (array_key_exists('dmrp_restitution', $data)) {
                $data['dmrp_restitution'] = $this->toBool($data['dmrp_restitution']);
            }

            // Gérer les colonnes JSON qui pourraient être string en V1
            foreach (['ouvrages_consigner_gmao', 'ouvrages_installer_gmao'] as $jsonCol) {
                if (isset($data[$jsonCol]) && is_string($data[$jsonCol])) {
                    $decoded = json_decode($data[$jsonCol], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $data[$jsonCol] = null;
                    }
                }
            }

            // Timestamps
            if (!isset($data['created_at'])) {
                $data['created_at'] = $v1Data['created_at'] ?? now();
            }
            if (!isset($data['updated_at'])) {
                $data['updated_at'] = $v1Data['updated_at'] ?? now();
            }

            // Gérer les doublons de numero_demande (V1 peut avoir des doublons)
            if (isset($data['numero_demande']) && $data['numero_demande']) {
                $originalNumero = $data['numero_demande'];
                $suffix = 2;
                while (DB::table('demandes')->where('numero_demande', $data['numero_demande'])->exists()) {
                    $data['numero_demande'] = $originalNumero . '-' . $suffix;
                    $suffix++;
                }
                if ($data['numero_demande'] !== $originalNumero) {
                    // Mettre à jour aussi le pdf_path pour refléter le nouveau numéro
                    if (isset($data['pdf_path'])) {
                        $data['pdf_path'] = str_replace($originalNumero, $data['numero_demande'], $data['pdf_path']);
                    }
                }
            }

            try {
                $newId = DB::table('demandes')->insertGetId($data);
                $this->demandeIdMap[$v1Row->id] = $newId;
                $inserted++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  Erreur demande V1 #{$v1Row->id}: " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✓ {$inserted} demandes insérées, {$errors} erreurs.");
    }

    private function migrateNotes(): void
    {
        if (!$this->v1TableExists('notes')) {
            $this->error('Table "notes" introuvable en V1.');
            return;
        }

        $v1Cols = $this->getV1Columns('notes');
        $v2Cols = $this->getV2Columns('notes');

        $this->showSchemaComparison('notes', $v1Cols, $v2Cols);

        $commonCols = array_intersect($v1Cols, $v2Cols);
        $colsToSkip = ['id'];
        $commonCols = array_diff($commonCols, $colsToSkip);

        $userFkCols = [
            'etabli_id', 'verifie_id', 'valide_id',
            'retourne1_id', 'retourne2_id',
            'execute_id', 'en_cours_execution_id', 'annule_id',
        ];

        $v1Notes = DB::connection($this->v1Connection)
            ->table('notes')
            ->orderBy('id')
            ->get();

        $this->info("  {$v1Notes->count()} notes trouvées en V1.");

        if ($this->option('dry-run')) {
            $this->warn('  [DRY-RUN] Aucune insertion effectuée.');
            return;
        }

        if (!$this->option('force') && !$this->confirm("Insérer {$v1Notes->count()} notes dans la V2 ?")) {
            $this->warn('  Annulé.');
            return;
        }

        $inserted = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($v1Notes->count());

        foreach ($v1Notes as $v1Row) {
            $data = [];
            $v1Data = (array) $v1Row;

            foreach ($commonCols as $col) {
                if (in_array($col, $userFkCols)) {
                    $data[$col] = $this->mapUserId($v1Data[$col] ?? null);
                } elseif ($col === 'demande_id') {
                    // Mapper l'ancien demande_id vers le nouveau
                    $data[$col] = $this->demandeIdMap[$v1Data[$col]] ?? null;
                } else {
                    $data[$col] = $v1Data[$col] ?? null;
                }
            }

            // demande_id est obligatoire — skip si non mappé
            if (empty($data['demande_id'])) {
                $this->newLine();
                $this->warn("  Skip note V1 #{$v1Row->id}: demande_id {$v1Data['demande_id']} non trouvé en V2.");
                $errors++;
                $bar->advance();
                continue;
            }

            // Adapter le statut
            if (isset($data['statut'])) {
                $data['statut'] = $this->mapNoteStatut($data['statut']);
            }

            // Timestamps
            if (!isset($data['created_at'])) {
                $data['created_at'] = $v1Data['created_at'] ?? now();
            }
            if (!isset($data['updated_at'])) {
                $data['updated_at'] = $v1Data['updated_at'] ?? now();
            }

            try {
                $newNoteId = DB::table('notes')->insertGetId($data);
                $this->noteIdMap[$v1Row->id] = $newNoteId;
                $inserted++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  Erreur note V1 #{$v1Row->id}: " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✓ {$inserted} notes insérées, {$errors} erreurs.");
    }

    private function migrateContacts(): void
    {
        // 1. Charges de consignation : V1 "chargecons" → V2 "charges_cons"
        if ($this->v1TableExists('chargecons')) {
            $v1Data = DB::connection($this->v1Connection)->table('chargecons')->orderBy('id')->get();
            $this->info("  {$v1Data->count()} charges de consignation en V1.");

            if (!$this->option('dry-run')) {
                foreach ($v1Data as $row) {
                    try {
                        $newId = DB::table('charges_cons')->insertGetId([
                            'nom' => $row->nom,
                            'fonction' => $row->fonction,
                            'adresse' => $row->adresse,
                            'matricule' => $row->matricule,
                            'telephone' => $row->telephone,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ]);
                        $this->chargeConsIdMap[$row->id] = $newId;
                    } catch (\Exception $e) {
                        $this->error("    Erreur chargecon V1 #{$row->id}: " . $e->getMessage());
                    }
                }
                $this->info("  ✓ " . count($this->chargeConsIdMap) . " charges_cons insérées.");
            }
        }

        // 2. Correspondants : V1 "correspondants" → V2 "correspondants"
        if ($this->v1TableExists('correspondants')) {
            $v1Data = DB::connection($this->v1Connection)->table('correspondants')->orderBy('id')->get();
            $this->info("  {$v1Data->count()} correspondants en V1.");

            if (!$this->option('dry-run')) {
                foreach ($v1Data as $row) {
                    try {
                        $newId = DB::table('correspondants')->insertGetId([
                            'nom' => $row->nom,
                            'fonction' => $row->fonction,
                            'adresse' => $row->adresse,
                            'matricule' => $row->matricule,
                            'telephone' => $row->telephone,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ]);
                        $this->correspondantIdMap[$row->id] = $newId;
                    } catch (\Exception $e) {
                        $this->error("    Erreur correspondant V1 #{$row->id}: " . $e->getMessage());
                    }
                }
                $this->info("  ✓ " . count($this->correspondantIdMap) . " correspondants insérés.");
            }
        }

        // 3. Services destinataires : V1 "servicedest" → V2 "services_dest"
        if ($this->v1TableExists('servicedest')) {
            $v1Data = DB::connection($this->v1Connection)->table('servicedest')->orderBy('id')->get();
            $this->info("  {$v1Data->count()} services destinataires en V1.");

            if (!$this->option('dry-run')) {
                foreach ($v1Data as $row) {
                    try {
                        $newId = DB::table('services_dest')->insertGetId([
                            'nom' => $row->nom,
                            'responsable' => $row->responsable,
                            'email' => $row->email,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ]);
                        $this->serviceDestIdMap[$row->id] = $newId;
                    } catch (\Exception $e) {
                        $this->error("    Erreur servicedest V1 #{$row->id}: " . $e->getMessage());
                    }
                }
                $this->info("  ✓ " . count($this->serviceDestIdMap) . " services_dest insérés.");
            }
        }
    }

    private function migratePivotTables(): void
    {
        if ($this->option('dry-run')) {
            $this->warn('  [DRY-RUN] Aucune insertion effectuée.');
            return;
        }

        // 1. note_charge_consignation
        if ($this->v1TableExists('note_charge_consignation')) {
            $v1Pivots = DB::connection($this->v1Connection)->table('note_charge_consignation')->get();
            $inserted = 0;
            foreach ($v1Pivots as $row) {
                $newNoteId = $this->noteIdMap[$row->note_id] ?? null;
                $newChargeId = $this->chargeConsIdMap[$row->chargecons_id] ?? null;
                if ($newNoteId && $newChargeId) {
                    try {
                        DB::table('note_charge_consignation')->insert([
                            'note_id' => $newNoteId,
                            'charge_cons_id' => $newChargeId,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ]);
                        $inserted++;
                    } catch (\Exception $e) {
                        $this->error("    Erreur pivot charge V1 #{$row->id}: " . $e->getMessage());
                    }
                }
            }
            $this->info("  ✓ {$inserted}/{$v1Pivots->count()} note_charge_consignation insérées.");
        }

        // 2. note_correspondant
        if ($this->v1TableExists('note_correspondant')) {
            $v1Pivots = DB::connection($this->v1Connection)->table('note_correspondant')->get();
            $inserted = 0;
            foreach ($v1Pivots as $row) {
                $newNoteId = $this->noteIdMap[$row->note_id] ?? null;
                $newCorrespId = $this->correspondantIdMap[$row->correspondant_id] ?? null;
                if ($newNoteId && $newCorrespId) {
                    try {
                        DB::table('note_correspondant')->insert([
                            'note_id' => $newNoteId,
                            'correspondant_id' => $newCorrespId,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ]);
                        $inserted++;
                    } catch (\Exception $e) {
                        $this->error("    Erreur pivot correspondant V1 #{$row->id}: " . $e->getMessage());
                    }
                }
            }
            $this->info("  ✓ {$inserted}/{$v1Pivots->count()} note_correspondant insérées.");
        }

        // 3. note_service
        if ($this->v1TableExists('note_service')) {
            $v1Pivots = DB::connection($this->v1Connection)->table('note_service')->get();
            $inserted = 0;
            foreach ($v1Pivots as $row) {
                $newNoteId = $this->noteIdMap[$row->note_id] ?? null;
                $newServiceId = $this->serviceDestIdMap[$row->servicedest_id] ?? null;
                if ($newNoteId && $newServiceId) {
                    try {
                        DB::table('note_service')->insert([
                            'note_id' => $newNoteId,
                            'service_dest_id' => $newServiceId,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ]);
                        $inserted++;
                    } catch (\Exception $e) {
                        $this->error("    Erreur pivot service V1 #{$row->id}: " . $e->getMessage());
                    }
                }
            }
            $this->info("  ✓ {$inserted}/{$v1Pivots->count()} note_service insérées.");
        }
    }

    /**
     * Reconstruit le mapping V1 demande_id → V2 demande_id
     * en se basant sur numero_demande (identifiant stable entre V1 et V2)
     */
    private function rebuildDemandeIdMap(): void
    {
        $v1Demandes = DB::connection($this->v1Connection)
            ->table('demandes')
            ->select('id', 'numero_demande')
            ->orderBy('id')
            ->get();

        $v2Demandes = DB::table('demandes')
            ->select('id', 'numero_demande')
            ->get()
            ->keyBy('numero_demande');

        $mapped = 0;
        foreach ($v1Demandes as $v1) {
            if ($v1->numero_demande && isset($v2Demandes[$v1->numero_demande])) {
                $this->demandeIdMap[$v1->id] = $v2Demandes[$v1->numero_demande]->id;
                $mapped++;
            }
        }

        $this->info("  ✓ {$mapped}/{$v1Demandes->count()} demandes mappées par numero_demande.");
    }

    /**
     * Récupère les signatures et stamps des users V1 pour les users V2
     * qui n'en ont pas encore. Ne remplace jamais une signature existante.
     */
    private function migrateSignatures(): void
    {
        $v1Users = DB::connection($this->v1Connection)
            ->table('users')
            ->select('id', 'user_matricule', 'email', 'name', 'signature', 'stamp')
            ->where(function ($q) {
                $q->whereNotNull('signature')->where('signature', '!=', '')
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('stamp')->where('stamp', '!=', '');
                  });
            })
            ->get();

        $this->info("  {$v1Users->count()} utilisateurs V1 avec signature ou stamp.");

        $updated = 0;
        $skipped = 0;

        foreach ($v1Users as $v1User) {
            $v2UserId = $this->mapUserId($v1User->id);
            if (!$v2UserId) {
                continue;
            }

            $v2User = DB::table('users')->where('id', $v2UserId)->first(['signature', 'stamp']);
            if (!$v2User) {
                continue;
            }

            $updates = [];

            if ($v1User->signature && !$v2User->signature) {
                $updates['signature'] = $v1User->signature;
            }
            if ($v1User->stamp && !$v2User->stamp) {
                $updates['stamp'] = $v1User->stamp;
            }

            if (!empty($updates)) {
                if (!$this->option('dry-run')) {
                    DB::table('users')->where('id', $v2UserId)->update($updates);
                }
                $fields = implode(', ', array_keys($updates));
                $this->line("  ✓ {$v1User->name} (#{$v2UserId}): {$fields}");
                $updated++;
            } else {
                $skipped++;
            }
        }

        $action = $this->option('dry-run') ? 'à mettre à jour' : 'mis à jour';
        $this->info("  ✓ {$updated} utilisateurs {$action}, {$skipped} ignorés (déjà renseigné).");
    }

    /**
     * En V1, les CT externes étaient dans la table users avec un email
     * du type *.externe@desa.local. En V2, ils vont dans charges_travaux.
     */
    private function migrateChargesTravaux(): void
    {
        // Identifier les CT externes par leur email (*.externe@*)
        $v1ExternalUsers = DB::connection($this->v1Connection)
            ->table('users')
            ->where('email', 'LIKE', '%.externe@%')
            ->orderBy('id')
            ->get();

        $this->info("  {$v1ExternalUsers->count()} users externes trouvés en V1 (email *.externe@*).");

        // Parmi eux, lesquels sont utilisés comme charge_travaux dans des demandes ?
        $v1CtIds = DB::connection($this->v1Connection)
            ->table('demandes')
            ->whereNotNull('charge_travaux_id')
            ->distinct()
            ->pluck('charge_travaux_id')
            ->toArray();

        $externalCtIds = $v1ExternalUsers->pluck('id')->toArray();
        $usedExternalCtIds = array_intersect($externalCtIds, $v1CtIds);

        $this->info("  → " . count($usedExternalCtIds) . " sont utilisés comme chargé de travaux dans des demandes.");

        $v1ExternalCTs = $v1ExternalUsers;
        $this->info("  {$v1ExternalCTs->count()} CT externes à insérer dans charges_travaux V2.");

        if ($this->option('dry-run')) {
            foreach ($v1ExternalCTs as $ct) {
                $this->line("    - #{$ct->id} {$ct->name} ({$ct->email})");
            }
            $this->warn('  [DRY-RUN] Aucune insertion effectuée.');
            return;
        }

        if (!$this->option('force') && !$this->confirm("Insérer {$v1ExternalCTs->count()} CT externes dans charges_travaux V2 ?")) {
            $this->warn('  Annulé.');
            return;
        }

        $inserted = 0;
        $errors = 0;

        foreach ($v1ExternalCTs as $v1User) {
            $v1Row = (array) $v1User;
            $data = [
                'nom' => $v1Row['name'] ?? $v1Row['nom'] ?? 'N/A',
                'telephone' => $v1Row['telephone'] ?? $v1Row['phone'] ?? $v1Row['tel'] ?? null,
                'entreprise' => $v1Row['entreprise'] ?? null,
                'service' => $v1Row['service'] ?? null,
                'actif' => true,
                'created_at' => $v1Row['created_at'] ?? now(),
                'updated_at' => $v1Row['updated_at'] ?? now(),
            ];

            try {
                $newId = DB::table('charges_travaux')->insertGetId($data);
                $this->chargeTravauxIdMap[$v1User->id] = $newId;
                $inserted++;
            } catch (\Exception $e) {
                $this->error("    Erreur CT externe V1 #{$v1User->id} ({$v1User->name}): " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("  ✓ {$inserted} CT externes insérés dans charges_travaux, {$errors} erreurs.");

        // Mettre à jour charge_travaux_externe_id dans les demandes V2
        $this->info('  Mise à jour de charge_travaux_externe_id dans les demandes V2...');

        $allExternalV1Ids = array_keys($this->chargeTravauxIdMap);
        $v1DemandesWithExternalCT = DB::connection($this->v1Connection)
            ->table('demandes')
            ->whereIn('charge_travaux_id', $allExternalV1Ids)
            ->select('id', 'charge_travaux_id')
            ->get();

        $updated = 0;
        foreach ($v1DemandesWithExternalCT as $v1Demande) {
            $v2DemandeId = $this->demandeIdMap[$v1Demande->id] ?? null;
            $v2ChargeTravauxId = $this->chargeTravauxIdMap[$v1Demande->charge_travaux_id] ?? null;

            if ($v2DemandeId && $v2ChargeTravauxId) {
                DB::table('demandes')
                    ->where('id', $v2DemandeId)
                    ->update([
                        'charge_travaux_externe_id' => $v2ChargeTravauxId,
                        'charge_travaux_id' => null,
                    ]);
                $updated++;
            }
        }

        $this->info("  ✓ {$updated} demandes mises à jour avec charge_travaux_externe_id.");
    }

    private function showSchemaComparison(string $table, array $v1Cols, array $v2Cols): void
    {
        $common = array_intersect($v1Cols, $v2Cols);
        $onlyV1 = array_diff($v1Cols, $v2Cols);
        $onlyV2 = array_diff($v2Cols, $v1Cols);

        $this->info("  Colonnes communes (" . count($common) . "): " . implode(', ', $common));

        if (!empty($onlyV1)) {
            $this->warn("  Colonnes V1 ignorées (" . count($onlyV1) . "): " . implode(', ', $onlyV1));
        }
        if (!empty($onlyV2)) {
            $this->comment("  Colonnes V2 absentes de V1 (" . count($onlyV2) . "): " . implode(', ', $onlyV2));
            $this->comment("  → Ces colonnes auront la valeur NULL/défaut.");
        }
        $this->newLine();
    }

    private function v1TableExists(string $table): bool
    {
        return DB::connection($this->v1Connection)
            ->getSchemaBuilder()
            ->hasTable($table);
    }

    // ==================== MAPPING DES VALEURS ENUM ====================

    private function mapDemandeStatut(?string $statut): string
    {
        if ($statut === null) {
            return 'créée';
        }

        $map = [
            // V1 value => V2 value (à adapter selon ta V1)
            'créée' => 'créée',
            'creee' => 'créée',
            'en cours de traitement' => 'en cours de traitement',
            'en_cours' => 'en cours de traitement',
            'acceptée' => 'acceptée',
            'acceptee' => 'acceptée',
            'retournée' => 'retournée',
            'retournee' => 'retournée',
            'brouillon' => 'brouillon',
        ];

        return $map[strtolower(trim($statut))] ?? 'créée';
    }

    private function mapNoteStatut(?string $statut): string
    {
        if ($statut === null) {
            return 'brouillon';
        }

        $map = [
            'brouillon' => 'brouillon',
            'en étude' => 'en étude',
            'en etude' => 'en étude',
            'en attente de vérification' => 'en attente de vérification',
            'en attente de verification' => 'en attente de vérification',
            'vérifiée' => 'vérifiée',
            'verifiee' => 'vérifiée',
            'en attente de validation' => 'en attente de validation',
            'validée' => 'validée',
            'validee' => 'validée',
            'en cours d\'exécution' => 'en cours d\'exécution',
            'en cours d\'execution' => 'en cours d\'exécution',
            'executée' => 'executée',
            'executee' => 'executée',
            'retournée' => 'retournée',
            'retournee' => 'retournée',
            'annulée' => 'annulée',
            'annulee' => 'annulée',
        ];

        return $map[strtolower(trim($statut))] ?? 'brouillon';
    }

    private function mapOuvrageType(?string $type): string
    {
        if ($type === null) {
            return 'ligne';
        }
        return in_array(strtolower($type), ['ligne', 'poste']) ? strtolower($type) : 'ligne';
    }

    private function mapModeSaisie(?string $mode): string
    {
        if ($mode === null) {
            return 'manuel';
        }
        return in_array(strtolower($mode), ['gmao', 'manuel']) ? strtolower($mode) : 'manuel';
    }

    private function mapEtape(?string $etape): string
    {
        if ($etape === null) {
            return 'ue';
        }
        return in_array(strtolower($etape), ['ue', 'de']) ? strtolower($etape) : 'ue';
    }

    private function toBool($value): bool
    {
        if ($value === null || $value === '' || $value === '0' || $value === 0 || $value === false || $value === '00:00:00') {
            return false;
        }
        return true;
    }

    private function disableForeignKeys(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            DB::statement('SET session_replication_role = \'replica\'');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        }
    }

    private function enableForeignKeys(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            DB::statement('SET session_replication_role = \'origin\'');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        }
    }
}
