<?php

namespace App\Helpers;

/**
 * Helper class for formatting currency values consistently across the
 * application. By default, it formats numbers in Moroccan Dirhams (MAD)
 * with two decimal places. The currency symbol is prefixed to the
 * formatted number, but this can be customised by passing parameters.
 */
class CurrencyHelper
{
    /**
     * Format a numeric value as currency.
     *
     * @param float|int $amount      The numeric amount to format.
     * @param int       $decimals    Number of decimal places. Defaults to 2.
     * @param string    $currency    The currency symbol or code to prepend. Defaults to 'MAD'.
     * @param bool      $symbolAfter Whether to place the currency symbol after the number. Defaults to false (symbol before).
     *
     * @return string The formatted currency string.
     */
    public static function format($amount, int $decimals = 2, string $currency = 'MAD', bool $symbolAfter = false): string
    {
        // Ensure the amount is numeric; cast to float for consistent formatting
        $numericAmount = (float) $amount;
        $formatted = number_format($numericAmount, $decimals);

        // Optionally place the currency symbol after the number instead of before
        if ($symbolAfter) {
            return $formatted . ' ' . $currency;
        }

        return $currency . ' ' . $formatted;
    }
}