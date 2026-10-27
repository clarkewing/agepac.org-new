<?php

use App\Filament\MenuItemTypes\LinkItemType;
use App\Filament\MenuItemTypes\PageItemType;
use App\Filament\MenuItemTypes\TextItemType;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\User;
use Datlechin\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
use Datlechin\FilamentMenuBuilder\Livewire\MenuItems;
use Datlechin\FilamentMenuBuilder\Models\Menu;
use Datlechin\FilamentMenuBuilder\Resources\MenuResource;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
});

it('lets navigation managers manage menus', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(MenuResource::getUrl())
        ->assertOk();
});

it('renders the builder with every item type', function () {
    $this->actingAs(User::factory()->admin()->create());

    $menu = Menu::create(['name' => 'Sidebar']);
    $group = MenuItem::create([
        'menu_id' => $menu->id,
        'type' => 'text',
        'title' => ['fr' => 'Réseau'],
        'order' => 1,
    ]);
    MenuItem::create([
        'menu_id' => $menu->id,
        'type' => 'model',
        'title' => ['fr' => 'Trombinoscopes'],
        'parent_id' => $group->id,
        'linkable_type' => Page::class,
        'linkable_id' => Page::factory()->create()->id,
        'order' => 2,
    ]);
    MenuItem::create([
        'menu_id' => $menu->id,
        'type' => 'link',
        'title' => ['fr' => 'Forum'],
        'url' => '/forum',
        'icon' => 'heroicon-o-chat-bubble-left-right',
        'order' => 3,
    ]);

    $this->get(MenuResource::getUrl('edit', ['record' => $menu]))
        ->assertOk();
});

it('is off limits without the navigation permission', function () {
    $this->actingAs(User::factory()->create(['approved_at' => now()]));

    $this->get(MenuResource::getUrl())
        ->assertForbidden();
});

it('reshapes the item form for editors', function () {
    $type = LinkItemType::make();
    $schema = collect($type->getFormSchema());
    $names = $schema->map(fn ($component): ?string => $component instanceof Field ? $component->getName() : null);

    // The icon moves out of Advanced, above our custom fields…
    expect($names->search('icon'))->toBeLessThan($names->search('data.badge'));

    // …as a select under upstream's own label.
    expect($schema[$names->search('icon')])->toBeInstanceOf(Select::class)
        ->getLabel()->toBe(__('filament-menu-builder::menu-builder.form.icon'));

    // Advanced keeps the remaining technical fields…
    expect(collect($type->getAdvancedSchema())->map->getName()->all())->toBe(['rel', 'classes']);

    // …and drops to just above the visibility toggle, which stays last.
    expect($schema[$schema->count() - 2])->toBeInstanceOf(Section::class)
        ->and($names->last())->toBe('is_visible');
});

it('leads the page item form with a not-live warning', function () {
    expect(PageItemType::make(Page::class)->getFormSchema()[0])
        ->toBeInstanceOf(Callout::class);

    $published = Page::factory()->create();
    $unpublished = Page::factory()->unpublished()->create();
    $trashed = tap(Page::factory()->create())->delete();

    expect(PageItemType::isNotLive(null))->toBeFalse()
        ->and(PageItemType::isNotLive($published->id))->toBeFalse()
        ->and(PageItemType::isNotLive($unpublished->id))->toBeTrue()
        ->and(PageItemType::isNotLive($trashed->id))->toBeTrue();
});

it('flags a childless group as broken', function () {
    $menu = Menu::create(['name' => 'Sidebar']);
    $group = MenuItem::create([
        'menu_id' => $menu->id,
        'type' => 'text',
        'title' => ['fr' => 'Réseau'],
        'order' => 1,
    ]);

    expect($group->isBroken())->toBeTrue();

    MenuItem::create([
        'menu_id' => $menu->id,
        'type' => 'link',
        'title' => ['fr' => 'Forum'],
        'url' => '/forum',
        'parent_id' => $group->id,
        'order' => 2,
    ]);

    expect($group->refresh()->isBroken())->toBeFalse();
});

it('leads the group form with an empty-group warning', function () {
    $schema = TextItemType::make()->getFormSchema();

    expect($schema[0])->toBeInstanceOf(Callout::class);
});

it('offers no icon on the group form', function () {
    $hasIconField = fn (array $schema): bool => collect($schema)
        ->contains(fn ($component): bool => $component instanceof Field && $component->getName() === 'icon');

    expect($hasIconField(TextItemType::make()->getFormSchema()))->toBeFalse()
        ->and($hasIconField(LinkItemType::make()->getFormSchema()))->toBeTrue();
});

it('picks icons as the blade icon names the sidebar renders', function () {
    $this->actingAs(User::factory()->admin()->create());

    $menu = Menu::create(['name' => 'Sidebar']);
    $item = MenuItem::create([
        'menu_id' => $menu->id,
        'type' => 'link',
        'title' => ['fr' => 'Forum'],
        'url' => '/forum',
        'icon' => 'heroicon-o-chat-bubble-left-right',
        'order' => 1,
    ]);

    Livewire::test(MenuItems::class, ['menu' => $menu])
        ->mountAction('editItem', ['item' => $item->id])
        ->assertActionDataSet(['icon' => 'heroicon-o-chat-bubble-left-right'])
        ->setActionData(['icon' => 'heroicon-o-star'])
        ->callMountedAction();

    expect($item->refresh()->icon)->toBe('heroicon-o-star');
});

it('offers outline heroicons under their plain names', function () {
    $options = collect(LinkItemType::make()->getFormSchema())
        ->first(fn ($component): bool => $component instanceof Field && $component->getName() === 'icon')
        ->getOptions();

    expect(array_keys($options))->each->toStartWith('heroicon-o-')
        ->and(trim(strip_tags($options['heroicon-o-arrow-right-start-on-rectangle'])))->toBe('Arrow right start on rectangle');
});

it('rejects icons outside the offered outline heroicons', function (string $icon) {
    $this->actingAs(User::factory()->admin()->create());

    $menu = Menu::create(['name' => 'Sidebar']);
    $item = MenuItem::create([
        'menu_id' => $menu->id,
        'type' => 'link',
        'title' => ['fr' => 'Forum'],
        'url' => '/forum',
        'order' => 1,
    ]);

    Livewire::test(MenuItems::class, ['menu' => $menu])
        ->callAction(TestAction::make('editItem')->arguments(['item' => $item->id]), ['icon' => $icon])
        ->assertHasActionErrors(['icon']);

    expect($item->refresh()->icon)->toBeNull();
})->with([
    'another icon set' => 'flag-country-fr',
    'another heroicon style' => 'heroicon-s-star',
]);

it('only lets group headings hold items', function () {
    $plugin = FilamentMenuBuilderPlugin::get();

    $menu = new Menu(['name' => 'Sidebar']);
    $heading = new MenuItem(['type' => 'text', 'title' => 'Réseau']);
    $link = new MenuItem(['type' => 'link', 'title' => 'Forum', 'url' => '/forum']);

    expect($plugin->isNestingRestricted())->toBeTrue()
        ->and($plugin->allowsNesting($link, $heading, $menu))->toBeTrue()
        ->and($plugin->allowsNesting($link, null, $menu))->toBeTrue()
        ->and($plugin->allowsNesting($heading, $heading, $menu))->toBeFalse()
        ->and($plugin->allowsNesting($link, $link, $menu))->toBeFalse();
});
