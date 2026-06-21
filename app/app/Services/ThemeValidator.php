<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

final class ThemeValidator
{
    public const DEFAULTS = [
        'paper' => '#fbfaf6', 'paper_2' => '#f4efe7', 'ink' => '#171412',
        'muted' => '#6f6256', 'green' => '#064e3b', 'green_2' => '#0f766e',
        'gold' => '#b45309', 'amber' => '#f59e0b', 'rose' => '#be3455', 'blue' => '#2563eb',
    ];

    public function validate(array $theme): array
    {
        $theme = array_merge(self::DEFAULTS, array_intersect_key($theme, self::DEFAULTS));
        foreach ($theme as $key => $color) {
            if (! is_string($color) || ! preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
                throw ValidationException::withMessages(["theme.$key" => 'صيغة اللون غير صحيحة.']);
            }
        }

        if ($this->contrast($theme['paper'], $theme['ink']) < 4.5) {
            throw ValidationException::withMessages(['theme.ink' => 'التباين بين النص والخلفية يجب أن يكون 4.5 على الأقل.']);
        }

        return $theme;
    }

    private function contrast(string $a, string $b): float
    {
        $l1 = $this->luminance($a); $l2 = $this->luminance($b);
        return (max($l1, $l2) + .05) / (min($l1, $l2) + .05);
    }

    private function luminance(string $hex): float
    {
        $parts = array_map(fn ($offset) => hexdec(substr($hex, $offset, 2)) / 255, [1, 3, 5]);
        $parts = array_map(fn ($v) => $v <= .03928 ? $v / 12.92 : (($v + .055) / 1.055) ** 2.4, $parts);
        return .2126 * $parts[0] + .7152 * $parts[1] + .0722 * $parts[2];
    }
}
