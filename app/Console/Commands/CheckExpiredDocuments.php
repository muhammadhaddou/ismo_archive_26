<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('documents:check-overdue')]
#[Description('Marque les documents en Temp_Out depuis plus de 48h comme Ecoule')]
class CheckExpiredDocuments extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des documents expirés...');

        $documents = \App\Models\Document::with('movements')
            ->where('status', 'Temp_Out')
            ->whereHas('latestSortie', function($q) {
                $q->where('deadline', '<', now());
            })->get();

        $count = 0;
        foreach ($documents as $doc) {
            /** @var \App\Models\Document $doc */
            $doc->update(['status' => 'Ecoule']);
            $count++;
            
            // Log in movement? Optional, but updating status is mainly what's needed.
        }

        $this->info("{$count} document(s) marqué(s) comme Écoulé.");
    }
}
