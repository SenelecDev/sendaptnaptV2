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

        foreach ($demandes as $demande) {
            try {
                if (!$dryRun) {
                    $this->regeneratePdf($demande);
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

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function regeneratePdf(Demande $demande): void
    {
        $schema = null;
        if (!empty($demande->schema)) {
            $schema = $this->convertImageToBase64(Storage::disk('public')->path($demande->schema));
        }

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
    }

    private function convertImageToBase64(string $path): ?string
    {
        if (!is_file($path)) {
            return null;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowed, true)) {
            return null;
        }

        $mime = $extension === 'jpg' ? 'jpeg' : $extension;
        $content = file_get_contents($path);
        if ($content === false) {
            return null;
        }

        return 'data:image/' . $mime . ';base64,' . base64_encode($content);
    }
}

