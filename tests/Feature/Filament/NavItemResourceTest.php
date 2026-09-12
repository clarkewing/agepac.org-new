<?php

use App\Enums\NavItemType;
use App\Filament\Resources\NavItems\NavItemResource;
use App\Filament\Resources\NavItems\Pages\ManageNavItems;
use App\Models\NavItem;
use App\Models\Page;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

it('lists nav items in display order', function () {
    $second = NavItem::factory()->url()->create(['order' => 2]);
    $first = NavItem::factory()->group()->create(['order' => 1]);

    Livewire::test(ManageNavItems::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$first, $second], inOrder: true);
});

it('creates a page item through the modal with the next order', function () {
    NavItem::factory()->url()->create(['order' => 7]);

    $page = Page::factory()->create();

    Livewire::test(ManageNavItems::class)
        ->callAction('create', [
            'type' => NavItemType::PAGE->value,
            'page_id' => $page->id,
        ])
        ->assertHasNoActionErrors();

    expect(NavItem::latest('id')->first())
        ->type->toBe(NavItemType::PAGE)
        ->page_id->toBe($page->id)
        ->order->toBe(8);
});

it('requires a label for groups and links but not pages', function () {
    Livewire::test(ManageNavItems::class)
        ->callAction('create', [
            'type' => NavItemType::URL->value,
            'url' => 'https://members.agepac.org/threads',
        ])
        ->assertHasActionErrors(['label.fr' => 'required']);
});

it('edits items in a slide-over', function () {
    $item = NavItem::factory()->url()->create();

    Livewire::test(ManageNavItems::class)
        ->callTableAction(EditAction::class, $item, [
            'label' => ['fr' => 'Ancien forum'],
            'target' => '_blank',
        ])
        ->assertHasNoTableActionErrors();

    expect($item->fresh())
        ->label->toBe(['fr' => 'Ancien forum'])
        ->target->toBe('_blank');
});

it('deletes items', function () {
    $item = NavItem::factory()->url()->create();

    Livewire::test(ManageNavItems::class)
        ->callTableAction(DeleteAction::class, $item);

    expect(NavItem::count())->toBe(0);
});

it('deletes a group together with its children', function () {
    $group = NavItem::factory()->group()->create();
    NavItem::factory()->url()->create(['parent_id' => $group->id]);

    $group->delete();

    expect(NavItem::count())->toBe(0);
});

it('reorders items by dragging', function () {
    $a = NavItem::factory()->url()->create(['order' => 1]);
    $b = NavItem::factory()->url()->create(['order' => 2]);

    Livewire::test(ManageNavItems::class)
        ->call('reorderTable', [(string) $b->id, (string) $a->id]);

    expect($b->fresh()->order)->toBeLessThan($a->fresh()->order);
});

it('is off limits without the navigation permission', function () {
    $this->actingAs(User::factory()->create());

    $this->get(NavItemResource::getUrl())
        ->assertForbidden();
});
