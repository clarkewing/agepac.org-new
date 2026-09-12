<?php

use App\Models\Page;
use App\Models\User;

it('renders published unrestricted pages for guests on the public domain', function () {
    $page = Page::factory()->unrestricted()->create(['body' => 'Du texte **important**.']);

    $this->get(route('public.pages.show', $page))
        ->assertOk()
        ->assertSee($page->title)
        ->assertSee('<strong>important</strong>', false);
});

it('redirects public pages from squawk to the public domain', function () {
    $page = Page::factory()->unrestricted()->create();

    $this->get(route('pages.show', $page))
        ->assertRedirect(route('public.pages.show', $page));
});

it('redirects restricted pages from the public domain to squawk', function () {
    $page = Page::factory()->create();

    $this->get(route('public.pages.show', $page))
        ->assertRedirect(route('pages.show', $page));
});

it('redirects guests to login for restricted pages', function () {
    $page = Page::factory()->create();

    $this->get(route('pages.show', $page))
        ->assertRedirect(route('login'));
});

it('forbids unapproved members on restricted pages', function () {
    $this->actingAs(User::factory()->unapproved()->create());

    $this->get(route('pages.show', Page::factory()->create()))
        ->assertForbidden();
});

it('shows restricted pages to approved members', function () {
    $this->actingAs(User::factory()->create());

    $page = Page::factory()->create();

    $this->get(route('pages.show', $page))
        ->assertOk()
        ->assertSee($page->title);
});

it('returns 404 for unpublished pages on both domains', function () {
    $page = Page::factory()->unrestricted()->unpublished()->create();

    $this->get(route('public.pages.show', $page))->assertNotFound();
    $this->get(route('pages.show', $page))->assertNotFound();

    $this->actingAs(User::factory()->create());

    $this->get(route('pages.show', $page))->assertNotFound();

    // Restricted pages only redirect to squawk once published, so their
    // existence never leaks through the public domain.
    $this->get(route('public.pages.show', Page::factory()->unpublished()->create()))
        ->assertNotFound();
});

it('lets page managers preview unpublished pages on squawk', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('pages.show', Page::factory()->unpublished()->create()))
        ->assertOk();

    // Public pages have no session on their own domain, so unpublished ones
    // are previewed on squawk too, without the redirect published ones get.
    $this->get(route('pages.show', Page::factory()->unrestricted()->unpublished()->create()))
        ->assertOk();
});

it('returns 404 for trashed pages', function () {
    $page = tap(Page::factory()->unrestricted()->create())->delete();

    $this->get(route('public.pages.show', $page))->assertNotFound();
});

it('ignores a trailing slash in the url', function () {
    $page = Page::factory()->unrestricted()->create();

    $this->get(route('public.pages.show', $page).'/')->assertOk();
});

it('serves paths containing slashes', function () {
    $page = Page::factory()->unrestricted()->create(['path' => 'policies/privacy']);

    $this->get(route('public.pages.show', $page))->assertOk();
});

it('feeds front matter description into the page head', function () {
    $page = Page::factory()->unrestricted()->create([
        'body' => "---\ndescription: Une page de test.\n---\n\nContenu",
    ]);

    $this->get(route('public.pages.show', $page))
        ->assertOk()
        ->assertSee('Une page de test.')
        ->assertDontSee('---');
});

it('shows an eyebrow above the title when front matter provides one', function () {
    $page = Page::factory()->unrestricted()->create([
        'body' => "---\neyebrow: Juridique\n---\n\nContenu",
    ]);

    $this->get(route('public.pages.show', $page))
        ->assertOk()
        ->assertSeeInOrder(['Juridique', $page->title]);

    $this->actingAs(User::factory()->create());

    $restricted = Page::factory()->create([
        'body' => "---\neyebrow: Carrière\n---\n\nContenu",
    ]);

    $this->get(route('pages.show', $restricted))
        ->assertOk()
        ->assertSeeInOrder(['Carrière', $restricted->title]);
});

it('renders html pages through the sanitizing pipeline', function () {
    $page = Page::factory()->html()->unrestricted()->create([
        'body' => '<p>Du contenu</p><script>alert("xss")</script>',
    ]);

    // The layout ships legitimate scripts, so assert on the payload: the
    // sanitizer drops blocked elements together with their contents.
    $this->get(route('public.pages.show', $page))
        ->assertOk()
        ->assertSee('Du contenu')
        ->assertDontSee('alert("xss")', false);
});
