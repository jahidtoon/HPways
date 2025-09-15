<?php

namespace App\Services;

class VisaTypeMapper
{
    /**
     * Map quiz terminal codes to internal visa_type codes using config/quiz.php.
     * Returns null if the terminal does not correspond to an actionable application.
     */
    public static function map(string $terminal): ?string
    {
        $mapping = config('quiz.terminal_to_visa_type', []);
        return $mapping[$terminal] ?? null;
    }

    /**
     * Whether a terminal represents an actionable (eligible) outcome.
     */
    public static function isActionable(string $terminal): bool
    {
        $actionable = config('quiz.actionable_terminals', []);
        return in_array($terminal, $actionable, true);
    }
}

