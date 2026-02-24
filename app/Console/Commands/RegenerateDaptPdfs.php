<?php

namespace App\Console\Commands;

use App\Models\Demande;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegenerateDaptPdfs extends Command
{
    protected $signature = 'dapt:regenerate-pdfs
                            {--id=* : IDs des demandes a regenerer}
                            {--all : Inclure aussi les demandes sans schema}
                            {--only-missing-pdf : Regenerer seulement les demandes sans pdf_path}
                            {--dry-run : Afficher ce qui sera fait sans ecrire}';

    protected $description = 'Regenerer les PDF DAPT (pdf_path) pour inclure le schema et les donnees a jour';

    public function handle(): int
    {
        $ids = array_filter((array) $this->option('id'));
        $includeAll = (bool) $this->option('all');
        $onlyMissingPdf = (bool) $this->option('only-missing-pdf');
        $dryRun = (bool) $this->option('dry-run');

        $query = Demande::with(['demandeur', 'chargeTravaux'])->orderBy('id');

        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }

        if (!$includeAll) {
            $query->whereNotNull('schema')->where('schema', '!=', '');
        }

        if ($onlyMissingPdf) {
            $query->where(function ($q) {
                $q->whereNull('pdf_path')->orWhere('pdf_path', '');
            });
        }

        $demandes = $query->get();

        if ($demandes->isEmpty()) {
            $this->warn('Aucune demande a traiter.');
            return self::SUCCESS;
        }

        $this->info('Demandes a traiter: ' . $demandes->count());
        if ($dryRun) {
            $this->warn('Mode dry-run actif: aucun fichier ne sera ecrit.');
        }

        $bar = $this->output->createProgressBar($demandes->count());
        $bar->start();

        $success = 0;
        $errors = 0;
        $schemaAsImage = 0;
        $schemaMissingFile = 0;
        $schemaUnsupported = 0;
        $schemaEmpty = 0;

        foreach ($demandes as $demande) {
            try {
                if (!$dryRun) {
                    $schemaStatus = $this->regeneratePdf($demande);
                    if ($schemaStatus === 'image') {
                        $schemaAsImage++;
                    } elseif ($schemaStatus === 'missing') {
                        $schemaMissingFile++;
                    } elseif ($schemaStatus === 'unsupported') {
                        $schemaUnsupported++;
                    } else {
                        $schemaEmpty++;
                    }
                }
                $success++;
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error("Erreur demande #{$demande->id} ({$demande->numero_demande}): {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Termine. Succes: {$success}, Erreurs: {$errors}");
        if (!$dryRun) {
            $this->line("Schemas integres (image): {$schemaAsImage}");
            $this->line("Schemas introuvables (fichier absent): {$schemaMissingFile}");
            $this->line("Schemas non integrables (extension non image): {$schemaUnsupported}");
            $this->line("Sans schema: {$schemaEmpty}");
        }

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function regeneratePdf(Demande $demande): string
    {
        [$schema, $schemaStatus] = $this->resolveSchemaBase64($demande->schema);

        $signatureN1 = null;
        $n1Id = $demande->demandeur->n1_id ?? null;
        if ($n1Id) {
            $n1 = User::find($n1Id);
            if ($n1 && !empty($n1->signature)) {
                $signatureN1 = $this->convertImageToBase64(Storage::disk('public')->path($n1->signature));
            }
        }

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $html = view('pdf.dapt', compact('demande', 'schema', 'signatureN1'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = 'demande_' . $demande->numero_demande . '.pdf';
        $filePath = 'pdfs/' . $fileName;

        // Eviter les vieux doublons pour le meme numero.
        $existingFiles = Storage::disk('public')->files('pdfs');
        foreach ($existingFiles as $file) {
            if (str_contains($file, 'demande_' . $demande->numero_demande)) {
                Storage::disk('public')->delete($file);
            }
        }

        Storage::disk('public')->put($filePath, $dompdf->output());
        $demande->pdf_path = $filePath;
        $demande->save();

        return $schemaStatus;
    }

    private function resolveSchemaBase64(?string $schemaValue): array
    {
        if (empty($schemaValue)) {
            return [null, 'empty'];
        }

        $candidates = [];
        $schemaValue = trim($schemaValue);

        if (preg_match('/^([a-zA-Z]:\\\\|\\/)/', $schemaValue)) {
            $candidates[] = $schemaValue;
        }

        $normalized = ltrim(str_replace('\\', '/', $schemaValue), '/');
        $candidates[] = Storage::disk('public')->path($normalized);
        if (str_starts_with($normalized, 'storage/')) {
            $normalizedWithoutStorage = substr($normalized, 8);
            $candidates[] = Storage::disk('public')->path($normalizedWithoutStorage);
            $candidates[] = public_path($normalized);
        } else {
            $candidates[] = public_path('storage/' . $normalized);
            $candidates[] = public_path($normalized);
        }

        $resolved = null;
        foreach (array_unique($candidates) as $candidate) {
            if (is_file($candidate)) {
                $resolved = $candidate;
                break;
            }
        }

        if ($resolved === null) {
            return [null, 'missing'];
        }

        $extension = strtolower(pathinfo($resolved, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowed, true)) {
            return [null, 'unsupported'];
        }

        $mime = $extension === 'jpg' ? 'jpeg' : $extension;
        $content = file_get_contents($resolved);
        if ($content === false) {
            return [null, 'missing'];
        }

        return ['data:image/' . $mime . ';base64,' . base64_encode($content), 'image'];
    }
}

