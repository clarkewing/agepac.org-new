<?php

namespace App\Actions;

use Illuminate\Support\Str;

class MakeUsername
{
    protected string $username;

    protected string $firstName;

    protected string $lastName;

    public function __invoke(string $firstName, string $lastName): string
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;

        $this->buildUsername();

        return $this->username;
    }

    protected function buildUsername(): void
    {
        $first = $this->normalizeString($this->firstName);
        $last = $this->normalizeString($this->lastName);

        $this->username = "$first.$last";
    }

    /**
     * Remove accents and normalize string
     */
    protected function normalizeString(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return Str::slug(preg_replace('/[^a-zA-Z]/', '', $value));
    }
}
