<?php

use App\Models\NavItem;
use App\Models\Page;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('renders nav items in the sidebar', function () {
    $page = Page::factory()->create(['title' => 'Trombinoscopes']);
    $group = NavItem::factory()->group()->create(['label' => ['fr' => 'Réseau'], 'order' => 1]);
    NavItem::factory()->create(['parent_id' => $group->id, 'page_id' => $page->id, 'order' => 2]);
    NavItem::factory()->url('https://members.agepac.org/threads')->create(['label' => ['fr' => 'Forum'], 'order' => 3]);

    $this->get(route('settings.appearance'))
        ->assertOk()
        ->assertSeeInOrder(['Réseau', 'Trombinoscopes', 'Forum'])
        ->assertSee($page->url())
        ->assertSee('https://members.agepac.org/threads');
});

it('hides disabled items and pages that are not live', function () {
    NavItem::factory()->url()->disabled()->create(['label' => ['fr' => 'Caché']]);
    NavItem::factory()->create(['page_id' => Page::factory()->unpublished()->create(['title' => 'Brouillon'])->id]);

    $this->get(route('settings.appearance'))
        ->assertOk()
        ->assertDontSee('Caché')
        ->assertDontSee('Brouillon');
});

it('hides groups whose items are all hidden', function () {
    $group = NavItem::factory()->group()->create(['label' => ['fr' => 'Vide']]);
    NavItem::factory()->url()->disabled()->create(['parent_id' => $group->id]);

    $this->get(route('settings.appearance'))
        ->assertOk()
        ->assertDontSee('Vide');
});
