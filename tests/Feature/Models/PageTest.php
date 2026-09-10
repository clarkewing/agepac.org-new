<?php

use App\Models\Page;

/*
|--------------------------------------------------------------------------
| Publication
|--------------------------------------------------------------------------
*/

it('is published once its publication date has passed', function () {
    expect(Page::factory()->make(['published_at' => now()->subMinute()]))
        ->isPublished()->toBeTrue();
});

it('is not published without a publication date', function () {
    expect(Page::factory()->unpublished()->make())
        ->isPublished()->toBeFalse();
});

it('is not published before a scheduled publication date', function () {
    expect(Page::factory()->make(['published_at' => now()->addDay()]))
        ->isPublished()->toBeFalse();
});

it('scopes queries to published pages', function () {
    $published = Page::factory()->create(['published_at' => now()->subMinute()]);
    Page::factory()->unpublished()->create();
    Page::factory()->create(['published_at' => now()->addDay()]);

    expect(Page::published()->get())
        ->toHaveCount(1)
        ->first()->toBe($published);
});
