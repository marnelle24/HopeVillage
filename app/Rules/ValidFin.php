<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFin implements ValidationRule
{
    /**
     * Validate a Singapore FIN.
     *
     * Format: [F|G|M] + 7 digits + checksum letter.
     *
     * Checksum notes:
     * - Uses the common NRIC/FIN Mod-11 weighting scheme.
     * - For FIN prefixes G (2000-2021) we add 4 to the weighted sum.
     * - For the new FIN prefix M (2022+), we currently treat it the same as G
     *   (add 4). If ICA updates the checksum algorithm, adjust here.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $fin = strtoupper(trim((string) $value));

        if (!preg_match('/^[FGM]\d{7}[A-Z]$/', $fin)) {
            $fail('The :attribute must be a valid FIN (e.g. F1234567X).');
            return;
        }

        $prefix = $fin[0];
        $digits = substr($fin, 1, 7);
        $checksum = $fin[8];

        $weights = [2, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 7; $i++) {
            $sum += ((int) $digits[$i]) * $weights[$i];
        }

        $offset = 0;
        if (in_array($prefix, ['G', 'M'], true)) {
            $offset = 4;
        }

        $sum += $offset;

        // Foreigners FIN checksum mapping (F/G/M)
        $mapping = 'XWUTRQPNMLK';
        $expected = $mapping[$sum % 11] ?? null;

        if ($expected === null || $checksum !== $expected) {
            $fail('The :attribute checksum is invalid.');
        }
    }
}
