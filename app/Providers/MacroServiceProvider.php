<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class MacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Str::macro('nameCase', function (string $value): string {
            // https://gist.github.com/timvisee/2ad754445ea63262eb3b903864c641cc

            // List of properly cased parts
            $casedParts = collect([
                "O'", "l'", "d'", 'St.', 'Mc', 'the', 'van', 'het', 'in', "'t", 'ten',
                'den', 'von', 'und', 'der', 'de', 'da', 'of', 'and', 'the', 'III', 'IV',
                'VI', 'VII', 'VIII', 'IX',
            ]);

            // Trim whitespace sequences to one space, append space to properly chunk
            $value = preg_replace('/\s+/', ' ', $value).' ';

            // Break value up into parts split by value separators
            $parts = preg_split('/( |-|O\'|l\'|d\'|St\\.|Mc)/i', $value, -1, PREG_SPLIT_DELIM_CAPTURE);

            // Chunk parts, use $casedParts or uppercase first, remove unfinished chunks
            $value = collect($parts)
                ->chunk(2)
                ->filter(fn (Collection $part): bool => $part->count() === 2)
                ->mapSpread(function (string $name, ?string $separator = null) use ($casedParts): array {
                    // Use a specified case for separator if set
                    $cased = $casedParts->first(fn (string $i): bool => strcasecmp($i, $separator) === 0);
                    $separator = $cased ?? $separator;

                    // Choose a specified part case, or uppercase first as default
                    $cased = $casedParts->first(fn (string $i): bool => strcasecmp($i, $name) === 0);

                    return [$cased ?? ucfirst(strtolower($name)), $separator];
                })
                ->map(fn (array $part): string => implode($part))
                ->join('');

            // Trim and return normalized value
            return trim($value);
        });
    }
}
