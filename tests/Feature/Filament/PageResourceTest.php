<?php

use App\Enums\PageFormat;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Livewire\Livewire;

beforeEach(function () {
    pest()->browser()->withHost(
        uri(PageResource::getUrl())->host()
    );

    $this->actingAs(User::factory()->admin()->create());
});

it('lists pages', function () {
    $pages = Page::factory()->count(3)->create();

    Livewire::test(ListPages::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($pages);
});

it('searches pages by title', function () {
    $page = Page::factory()->create(['title' => 'Trombinoscopes']);
    $other = Page::factory()->create(['title' => 'Aide']);

    Livewire::test(ListPages::class)
        ->searchTable('Trombino')
        ->assertCanSeeTableRecords([$page])
        ->assertCanNotSeeTableRecords([$other]);
});

it('marks each title with a lock reflecting the restricted state', function () {
    $restricted = Page::factory()->create();
    $unrestricted = Page::factory()->unrestricted()->create();

    Livewire::test(ListPages::class)
        ->assertTableColumnExists('title', fn (TextColumn $column): bool => $column->getIcon($column->getState()) === Heroicon::LockClosed
            && $column->getIconColor($column->getState()) === null, $restricted)
        ->assertTableColumnExists('title', fn (TextColumn $column): bool => $column->getIcon($column->getState()) === Heroicon::LockOpen
            && $column->getIconColor($column->getState()) === 'danger', $unrestricted);
});

it('sorts pages by path by default', function () {
    $second = Page::factory()->create(['path' => 'bbb']);
    $first = Page::factory()->create(['path' => 'aaa']);

    Livewire::test(ListPages::class)
        ->assertCanSeeTableRecords([$first, $second], inOrder: true);
});

it('hides trashed pages until the trashed filter is used', function () {
    $active = Page::factory()->create();
    $trashed = Page::factory()->trashed()->create();

    Livewire::test(ListPages::class)
        ->assertCanSeeTableRecords([$active])
        ->assertCanNotSeeTableRecords([$trashed])
        ->filterTable('trashed', false)
        ->assertCanSeeTableRecords([$trashed])
        ->assertCanNotSeeTableRecords([$active]);
});

it('filters pages by format', function () {
    $markdown = Page::factory()->create();
    $html = Page::factory()->html()->create();

    Livewire::test(ListPages::class)
        ->filterTable('format', PageFormat::MARKDOWN->value)
        ->assertCanSeeTableRecords([$markdown])
        ->assertCanNotSeeTableRecords([$html])
        ->filterTable('format', PageFormat::HTML->value)
        ->assertCanSeeTableRecords([$html])
        ->assertCanNotSeeTableRecords([$markdown]);
});

it('filters pages by visibility', function () {
    $restricted = Page::factory()->create();
    $public = Page::factory()->unrestricted()->create();

    Livewire::test(ListPages::class)
        ->filterTable('restricted', true)
        ->assertCanSeeTableRecords([$restricted])
        ->assertCanNotSeeTableRecords([$public])
        ->filterTable('restricted', false)
        ->assertCanSeeTableRecords([$public])
        ->assertCanNotSeeTableRecords([$restricted]);
});

it('badges the publication state as published, scheduled, or unpublished', function () {
    $published = Page::factory()->create();
    $scheduled = Page::factory()->create(['published_at' => now()->addDay()]);
    $unpublished = Page::factory()->unpublished()->create();

    $color = fn (TextColumn $column): string|array|null => $column->getColor($column->getState());

    Livewire::test(ListPages::class)
        ->assertTableColumnExists('published_at', fn (TextColumn $column): bool => $color($column) === 'success' && $column->getIcon($column->getState()) === Heroicon::Check, $published)
        ->assertTableColumnExists('published_at', fn (TextColumn $column): bool => $color($column) === 'warning' && $column->getIcon($column->getState()) === Heroicon::Clock, $scheduled)
        ->assertTableColumnExists('published_at', fn (TextColumn $column): bool => $color($column) === 'gray', $unpublished)
        ->assertTableColumnFormattedStateSet('published_at', 'Unpublished', $unpublished);
});

it('creates a page with sensible defaults', function () {
    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'Trombinoscopes',
            'path' => 'trombinoscopes',
            'body' => '## Les promotions',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect(PageResource::getUrl());

    expect(Page::sole())
        ->title->toBe('Trombinoscopes')
        ->path->toBe('trombinoscopes')
        ->body->toBe('## Les promotions')
        ->format->toBe(PageFormat::MARKDOWN)
        ->restricted->toBeTrue()
        ->isPublished()->toBeTrue();
});

it('can create html pages', function () {
    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'Trombinoscopes',
            'path' => 'trombinoscopes',
            'format' => PageFormat::HTML->value,
            'body' => '<p>Les promotions</p>',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Page::sole())
        ->format->toBe(PageFormat::HTML)
        ->body->toBe('<p>Les promotions</p>');
});

it('fills the publication date with the current utc time', function () {
    $this->freezeTime();

    Livewire::test(CreatePage::class)
        ->fillForm(['published_at' => null])
        ->callFormComponentAction('published_at', 'now')
        ->assertSet('data.published_at', now()->format('Y-m-d H:i:s'));
});

it('creates an unpublished draft when the publication date is cleared', function () {
    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'Brouillon',
            'path' => 'brouillon',
            'body' => 'En cours de rédaction.',
        ])
        ->callFormComponentAction('published_at', 'clear')
        ->assertSet('data.published_at', null)
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Page::sole())
        ->published_at->toBeNull()
        ->isPublished()->toBeFalse();
});

it('edits markdown pages with the markdown editor and html pages with a code editor', function () {
    $markdown = Page::factory()->create();
    $html = Page::factory()->html()->create();

    Livewire::test(EditPage::class, ['record' => $markdown->id])
        ->assertFormFieldVisible('body')
        ->assertFormFieldHidden('html_body');

    Livewire::test(EditPage::class, ['record' => $html->id])
        ->assertFormFieldHidden('body')
        ->assertFormFieldVisible('html_body');
});

it('converts the body when switching format', function () {
    Livewire::test(CreatePage::class)
        ->fillForm(['body' => '## Titre'])
        ->set('data.format', PageFormat::HTML->value)
        ->assertFormSet(function (array $state): void {
            expect($state['body'])->toContain('<h2>Titre</h2>');
        });

    Livewire::test(CreatePage::class)
        ->set('data.format', PageFormat::HTML->value)
        ->fillForm(['body' => '<h2>Titre</h2>'])
        ->set('data.format', PageFormat::MARKDOWN->value)
        ->assertFormSet(function (array $state): void {
            expect($state['body'])->toContain('## Titre');
        });
});

it('styles front matter as metadata in the markdown editor', function () {
    $page = visit(PageResource::getUrl('create', isAbsolute: false))
        ->assertMissing('.cm-front-matter')
        ->type('.CodeMirror textarea', "---\ndescription: Une page.\n---\nContenu")
        ->assertVisible('.cm-front-matter >> nth=0')
        ->assertNoJavaScriptErrors();

    // Every front-matter line must read uniformly: no token span may keep
    // its own color, weight, or an opacity that would compound the line's.
    expect($page->script(<<<'JS'
        [...document.querySelectorAll('.cm-front-matter')].every((line) => {
            const lineStyle = getComputedStyle(line)

            return [...line.querySelectorAll('span')].every((span) => {
                const spanStyle = getComputedStyle(span)

                return spanStyle.opacity === '1'
                    && spanStyle.color === lineStyle.color
                    && spanStyle.fontWeight === lineStyle.fontWeight
            })
        })
    JS))->toBeTrue();
});

it('warns when a public page references attachments', function () {
    visit(PageResource::getUrl('create', isAbsolute: false))
        ->type('.CodeMirror textarea', 'Voir /attachments/abc/document.pdf')
        ->assertMissing('.fi-callout.fi-color-danger')
        ->assertDontSee(__('admin.pages.warnings.public_attachments.title'))
        ->click('Public')
        ->assertVisible('.fi-callout.fi-color-danger')
        ->assertSee(__('admin.pages.warnings.public_attachments.title'))
        ->assertSeeIn('.fi-callout.fi-color-danger', '/attachments/abc/document.pdf')
        ->assertNoJavaScriptErrors();

    $htmlPage = Page::factory()->unrestricted()->html()->create(['body' => '']);
    visit(PageResource::getUrl('edit', ['record' => $htmlPage], isAbsolute: false))
        ->assertMissing('.fi-callout.fi-color-danger')
        ->assertDontSee(__('admin.pages.warnings.public_attachments.title'))
        ->type('.cm-content', '<p>Voir <a href="/attachments/abc/document.pdf">l’attachement</a></p>')
        ->assertVisible('.fi-callout.fi-color-danger')
        ->assertSee(__('admin.pages.warnings.public_attachments.title'))
        ->assertSeeIn('.fi-callout.fi-color-danger', '/attachments/abc/document.pdf')
        ->assertNoJavaScriptErrors();
});

it('shows a conversion warning only once the body has content', function () {
    visit(PageResource::getUrl('create', isAbsolute: false))
        ->assertMissing('.fi-callout.fi-color-warning')
        ->assertDontSee(__('admin.pages.warnings.converted'))
        ->type('.CodeMirror textarea', 'Bonjour')
        ->assertVisible('.fi-callout.fi-color-warning')
        ->assertSee(__('admin.pages.warnings.converted'))
        ->assertNoJavaScriptErrors();

    $htmlPage = Page::factory()->html()->create(['body' => '']);
    visit(PageResource::getUrl('edit', ['record' => $htmlPage], isAbsolute: false))
        ->assertMissing('.fi-callout.fi-color-warning')
        ->assertDontSee(__('admin.pages.warnings.converted'))
        ->type('.cm-content', '<p>Bonjour</p>')
        ->assertVisible('.fi-callout.fi-color-warning')
        ->assertSee(__('admin.pages.warnings.converted'))
        ->assertNoJavaScriptErrors();
});

it('rejects malformed paths', function (string $path) {
    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'Aide',
            'path' => $path,
            'body' => 'Contenu',
        ])
        ->call('create')
        ->assertHasFormErrors(['path']);
})->with([
    'uppercase and spaces' => 'Not A Path',
    'trailing slash' => 'foo/',
    'leading slash' => '/foo',
    'doubled slash' => 'foo//bar',
]);

it('rejects duplicate paths', function () {
    Page::factory()->create(['path' => 'help']);

    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'Aide',
            'path' => 'help',
            'body' => 'Contenu',
        ])
        ->call('create')
        ->assertHasFormErrors(['path' => 'unique']);
});

it('requires a body', function () {
    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'Aide',
            'path' => 'aide',
        ])
        ->call('create')
        ->assertHasFormErrors(['body' => 'required']);
});

it('rejects taking another page’s path on update', function () {
    Page::factory()->create(['path' => 'help']);
    $page = Page::factory()->create(['path' => 'contact']);

    Livewire::test(EditPage::class, ['record' => $page->id])
        ->fillForm(['path' => 'help'])
        ->call('save')
        ->assertHasFormErrors(['path' => 'unique']);
});

it('links to records by id so slashed paths cannot break admin urls', function () {
    $page = Page::factory()->create(['path' => 'policies/privacy']);

    $url = PageResource::getUrl('edit', ['record' => $page]);

    expect($url)->toEndWith("/admin/pages/{$page->id}/edit");

    $this->get($url)->assertSuccessful();
});

it('updates a page', function () {
    $page = Page::factory()->create();

    Livewire::test(EditPage::class, ['record' => $page->id])
        ->fillForm(['title' => 'Titre mis à jour'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect(PageResource::getUrl());

    expect($page->refresh()->title)->toBe('Titre mis à jour');
});

it('soft deletes a page', function () {
    $page = Page::factory()->create();

    Livewire::test(EditPage::class, ['record' => $page->id])
        ->callAction(DeleteAction::class);

    expect($page->refresh()->trashed())->toBeTrue();
});

it('restores a trashed page', function () {
    $page = Page::factory()->trashed()->create();

    Livewire::test(EditPage::class, ['record' => $page->id])
        ->callAction(RestoreAction::class);

    expect($page->refresh()->trashed())->toBeFalse();
});

it('titles the create page with a proper article in both locales', function () {
    $this->get(PageResource::getUrl('create'))
        ->assertSee('Create a page')
        ->assertSee('Create & add another');

    $this->withSession(['locale' => 'fr'])
        ->get(PageResource::getUrl('create'))
        ->assertSee('Créer une page')
        ->assertSee('Créer puis en ajouter une autre');
});

it('forbids users without the pages permission', function () {
    $page = Page::factory()->create();

    $this->actingAs(User::factory()->create());

    $this->get(PageResource::getUrl())->assertForbidden();
    $this->get(PageResource::getUrl('create'))->assertForbidden();
    $this->get(PageResource::getUrl('edit', ['record' => $page]))->assertForbidden();
});
