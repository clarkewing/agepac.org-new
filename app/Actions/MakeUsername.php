<?php

namespace App\Actions;

use Illuminate\Support\Str;

class MakeUsername
{
    public function __invoke(string $firstName, string $lastName): string
    {
        $first = $this->normalize($firstName);
        $last = $this->normalize($lastName);

        return "$first.$last";
    }

    /**
     * Remove accents and normalize string
     */
    protected function normalize(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return Str::slug(preg_replace('/[^a-zA-Z]/', '', $value));
    }
}
