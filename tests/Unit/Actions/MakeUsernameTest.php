<?php

use App\Actions\MakeUsername;

beforeEach(function () {
    $this->action = new MakeUsername;
});

it('generates a simple username in first.last format', function () {
    $username = ($this->action)('John', 'Doe');

    expect($username)->toBe('john.doe');
});

it('removes accented characters', function () {
    $username = ($this->action)('Élodie', 'Müller');

    expect($username)->toBe('elodie.muller');
});

it('removes special characters and spaces', function () {
    $username = ($this->action)(' Jean-Pierre ', ' d’Hôpital ');

    expect($username)->toBe('jeanpierre.dhopital');
});

it('handles names with mixed casing and diacritics', function () {
    $username = ($this->action)('ÁlVarO', 'De La CRÚZ');

    expect($username)->toBe('alvaro.delacruz');
});
