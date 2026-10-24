<?php

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\User;
use Datlechin\FilamentMenuBuilder\Models\Menu;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();

    $this->actingAs(User::factory()->create());

    $this->menu = Menu::create(['name' => 'Sidebar']);
    $this->menu->locations()->create(['location' => 'sidebar']);
});

function createMenuItem(array $attributes): MenuItem
{
    return MenuItem::create([
        'menu_id' => test()->menu->id,
        'order' => MenuItem::max('order') + 1,
        ...$attributes,
    ]);
}

it('renders groups and items from the sidebar menu', function () {
    $page = Page::factory()->create(['title' => 'Trombinoscopes']);

    $group = createMenuItem([
        'type' => 'text',
        'title' => ['fr' => 'Réseau'],
    ]);
    createMenuItem([
        'type' => 'model',
        'title' => ['fr' => 'Trombinoscopes'],
        'parent_id' => $group->id,
        'linkable_type' => Page::class,
        'linkable_id' => $page->id,
    ]);
    createMenuItem([
        'type' => 'link',
        'title' => ['fr' => 'Forum'],
        'url' => 'https://members.agepac.org/threads',
        'icon' => 'heroicon-o-chat-bubble-left-right',
        'data' => ['badge' => '3', 'badge_color' => 'amber'],
    ]);

    $this->get(route('settings.appearance'))
        ->assertOk()
        ->assertSeeInOrder(['Réseau', 'Trombinoscopes', 'Forum'])
        ->assertSeeHtmlInOrder([$page->url(), 'https://members.agepac.org/threads'])
        ->assertSee('amber');
});

it('prefers the title for the current locale', function () {
    createMenuItem([
        'type' => 'link',
        'title' => ['fr' => 'Forum des anciens', 'en' => 'Alumni board'],
        'url' => '/forum',
    ]);

    app()->setLocale('fr');

    $this->get(route('settings.appearance'))
        ->assertOk()
        ->assertSee('Forum des anciens')
        ->assertDontSee('Alumni board');

    app()->setLocale('en');

    $this->get(route('settings.appearance'))
        ->assertOk()
        ->assertSee('Alumni board')
        ->assertDontSee('Forum des anciens');
});

it('hides invisible items', function () {
    createMenuItem([
        'type' => 'link',
        'title' => ['fr' => 'Masqué'],
        'url' => '/masque',
        'is_visible' => false,
    ]);

    $this->get(route('settings.appearance'))
        ->assertOk()
        ->assertDontSee('Masqué')
        ->assertDontSeeHtml('/masque');
});

it('hides page items whose page is not live', function () {
    $unpublished = Page::factory()->unpublished()->create();
    $trashed = tap(Page::factory()->create())->delete();

    createMenuItem([
        'type' => 'model',
        'title' => ['fr' => 'Brouillon'],
        'linkable_type' => Page::class,
        'linkable_id' => $unpublished->id,
    ]);
    createMenuItem([
        'type' => 'model',
        'title' => ['fr' => 'Corbeille'],
        'linkable_type' => Page::class,
        'linkable_id' => $trashed->id,
    ]);

    $this->get(route('settings.appearance'))
        ->assertOk()
        ->assertDontSee('Brouillon')
        ->assertDontSee('Corbeille');
});

it('expands a collapsed group holding the current page', function () {
    $group = createMenuItem([
        'type' => 'text',
        'title' => ['fr' => 'Réglages'],
        'data' => ['expandable' => true, 'expanded' => false],
    ]);
    createMenuItem([
        'type' => 'link',
        'title' => ['fr' => 'Apparence'],
        'url' => route('settings.appearance'),
        'parent_id' => $group->id,
    ]);

    $isOpen = fn (): bool => (bool) preg_match(
        '/<ui-disclosure[^>]*\bopen\b[^>]*data-flux-navlist-group/',
        $this->get(route('settings.appearance'))->assertOk()->getContent(),
    );

    expect($isOpen())->toBeTrue();

    // Drop the appearance item
    $group->children()
        ->where('url', route('settings.appearance'))
        ->delete();
    // Add another item so the group still has children
    createMenuItem([
        'type' => 'link',
        'title' => ['fr' => 'Ailleurs'],
        'url' => '/ailleurs',
        'parent_id' => $group->id,
    ]);

    expect($isOpen())->toBeFalse();
});

it('hides groups with no items', function () {
    createMenuItem([
        'type' => 'text',
        'title' => ['fr' => 'Creux'],
    ]);

    $this->get(route('settings.appearance'))
        ->assertOk()
        ->assertDontSee('Creux');
});

it('hides groups whose items are all hidden', function () {
    $group = createMenuItem([
        'type' => 'text',
        'title' => ['fr' => 'Vide'],
    ]);
    createMenuItem([
        'type' => 'link',
        'title' => ['fr' => 'Masqué'],
        'url' => '/masque',
        'parent_id' => $group->id,
        'is_visible' => false,
    ]);

    $this->get(route('settings.appearance'))
        ->assertOk()
        ->assertDontSee('Vide');
});
