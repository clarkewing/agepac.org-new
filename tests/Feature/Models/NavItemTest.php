<?php

use App\Models\NavItem;
use App\Models\Page;

/*
|--------------------------------------------------------------------------
| Labels
|--------------------------------------------------------------------------
*/

it('prefers the label for the current locale', function () {
    $item = NavItem::factory()->url()->make(['label' => ['fr' => 'Forum', 'en' => 'Board']]);

    app()->setLocale('fr');
    expect($item->label())->toBe('Forum');

    app()->setLocale('en');
    expect($item->label())->toBe('Board');
});

it('falls back to the fallback locale then the page title', function () {
    app()->setLocale('en');

    expect(NavItem::factory()->url()->make(['label' => ['fr' => 'Forum']])->label())
        ->toBe('Forum');

    $page = Page::factory()->create(['title' => 'Trombinoscopes']);

    expect(NavItem::factory()->create(['page_id' => $page->id, 'label' => null])->label())
        ->toBe('Trombinoscopes');
});

/*
|--------------------------------------------------------------------------
| Links
|--------------------------------------------------------------------------
*/

it('links page items to the page and url items to their url', function () {
    $page = Page::factory()->create();

    expect(NavItem::factory()->create(['page_id' => $page->id])->linkUrl())
        ->toBe($page->url())
        ->and(NavItem::factory()->url('https://members.agepac.org/threads')->make()->linkUrl())
        ->toBe('https://members.agepac.org/threads')
        ->and(NavItem::factory()->group()->make()->linkUrl())
        ->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Visibility
|--------------------------------------------------------------------------
*/

it('hides disabled items', function () {
    expect(NavItem::factory()->url()->disabled()->make()->isVisible())->toBeFalse()
        ->and(NavItem::factory()->url()->make()->isVisible())->toBeTrue();
});

it('hides page items without a living published page', function () {
    expect(NavItem::factory()->create()->isVisible())->toBeTrue();

    $unpublished = NavItem::factory()->create([
        'page_id' => Page::factory()->unpublished()->create()->id,
    ]);

    expect($unpublished->isVisible())->toBeFalse();

    $trashed = NavItem::factory()->create();
    $trashed->page->delete();

    expect($trashed->fresh()->isVisible())->toBeFalse();
});
