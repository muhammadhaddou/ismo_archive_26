<?php

namespace App\Services;

use App\Models\Movement;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReferenceGeneratorService
{
    /**
     * Generate a unique reference number for a withdrawal.
     * Format: Bac-[D/T]-[StudyYear]-[WithdrawalYear]-[AutoIncrement]
     * e.g. Bac-D-25-27-0001
     * 
     * @param string $withdrawalType 'D' (Définitif) or 'T' (Temporaire)
     * @param int|string $studyYear e.g., 25 for 2025
     * @param int|string|null $withdrawalYear e.g., 27 for 2027 (defaults to current year)
     * @return string
     */
    public function generate(string $withdrawalType, $studyYear, $withdrawalYear = null): string
    {
        $withdrawalYear = $withdrawalYear ?? Carbon::now()->format('y');
        // Ensure 2 digits for study year (e.g. from 2025-2026 takes 26, from 2025 takes 25)
        $studyYearStr = is_string($studyYear) ? substr($studyYear, -2) : str_pad($studyYear % 100, 2, '0', STR_PAD_LEFT);
        $withdrawalYearStr = str_pad(substr($withdrawalYear, -2), 2, '0', STR_PAD_LEFT);
        
        $prefix = "Bac-{$withdrawalType}-{$studyYearStr}-{$withdrawalYearStr}-";

        // Find the latest reference matching this prefix
        $latestMovement = Movement::query()
            ->where('reference_number', 'LIKE', "{$prefix}%")
            ->orderBy('reference_number', 'desc')
            ->first();

        if ($latestMovement && $latestMovement->reference_number) {
            // Extract the last 4 digits
            $lastIncrement = (int) substr($latestMovement->reference_number, -4);
            $newIncrement = $lastIncrement + 1;
        } else {
            $newIncrement = 1;
        }

        $formattedIncrement = str_pad($newIncrement, 4, '0', STR_PAD_LEFT);

        return $prefix . $formattedIncrement;
    }
}
