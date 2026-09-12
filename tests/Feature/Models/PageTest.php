<?php

use App\Models\Page;

/*
|--------------------------------------------------------------------------
| URLs
|--------------------------------------------------------------------------
*/

it('lives on the public site once published and unrestricted', function () {
    expect(Page::factory()->unrestricted()->make(['path' => 'about'])->url())
        ->toBe(route('public.pages.show', 'about'));
});

it('lives on squawk while restricted or unpublished', function () {
    expect(Page::factory()->make(['path' => 'epl20'])->url())
        ->toBe(route('pages.show', 'epl20'))
        ->and(Page::factory()->unrestricted()->unpublished()->make(['path' => 'draft'])->url())
        ->toBe(route('pages.show', 'draft'));
});

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

it('is scheduled only when publication is set for the future', function () {
    expect(Page::factory()->make(['published_at' => now()->addDay()])->isScheduled())->toBeTrue()
        ->and(Page::factory()->make(['published_at' => now()->subMinute()])->isScheduled())->toBeFalse()
        ->and(Page::factory()->unpublished()->make()->isScheduled())->toBeFalse();
});

it('scopes queries to published pages', function () {
    $published = Page::factory()->create(['published_at' => now()->subMinute()]);
    Page::factory()->unpublished()->create();
    Page::factory()->create(['published_at' => now()->addDay()]);

    expect(Page::published()->get())
        ->toHaveCount(1)
        ->first()->toBe($published);
});
