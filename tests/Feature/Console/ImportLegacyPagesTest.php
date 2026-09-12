<?php

use App\Enums\PageFormat;
use App\Models\Attachment;
use App\Models\Page;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

beforeEach(function () {
    config()->set('database.connections.legacy', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
    ]);

    DB::purge('legacy');
    DB::reconnect('legacy');

    Schema::connection('legacy')->create('pages', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('path')->unique();
        $table->text('body');
        $table->boolean('restricted');
        $table->timestamp('published_at')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
});

function seedLegacyPage(array $attributes = []): void
{
    DB::connection('legacy')->table('pages')->insert([
        'title' => 'Aide',
        'path' => 'help',
        'body' => "<!-- wp:heading -->\n<h2>Titre</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p>Du texte avec un <a href=\"https://agepac.org\">lien</a>.</p>\n<!-- /wp:paragraph -->",
        'restricted' => 0,
        'published_at' => '2026-08-08 19:08:24',
        'created_at' => '2020-07-14 00:00:00',
        'updated_at' => '2021-01-01 00:00:00',
        'deleted_at' => null,
        ...$attributes,
    ]);
}

function fakePng(): string
{
    return UploadedFile::fake()->image('fake.png')->getContent();
}

it('imports cleanly converting pages as markdown', function () {
    seedLegacyPage();

    $this->artisan('pages:import-legacy')
        ->expectsOutputToContain('1 pages: 1 converted cleanly to markdown')
        ->assertSuccessful();

    $page = Page::sole();

    expect($page)
        ->path->toBe('help')
        ->title->toBe('Aide')
        ->format->toBe(PageFormat::MARKDOWN)
        ->restricted->toBeFalse()
        ->body->toContain('## Titre')
        ->body->toContain('[lien](https://agepac.org)')
        ->body->not->toContain('wp:')
        ->published_at->toEqual(Carbon::parse('2026-08-08 19:08:24'))
        ->created_at->toEqual(Carbon::parse('2020-07-14 00:00:00'))
        ->updated_at->toEqual(Carbon::parse('2021-01-01 00:00:00'))
        ->deleted_at->toBeNull();
});

it('keeps pages that would lose content as html with their body verbatim', function () {
    seedLegacyPage([
        'path' => 'contact',
        'body' => $body = "<!-- wp:paragraph {\"align\":\"center\"} -->\n<p style=\"text-align:center\">Centré</p>\n<!-- /wp:paragraph -->",
    ]);

    $this->artisan('pages:import-legacy')
        ->expectsOutputToContain('1 differ by alignment only')
        ->assertSuccessful();

    $page = Page::sole();

    expect($page)
        ->format->toBe(PageFormat::HTML)
        ->body->toBe($body);
});

it('keeps soft-deleted legacy pages soft-deleted', function () {
    seedLegacyPage(['deleted_at' => '2022-05-01 00:00:00']);

    $this->artisan('pages:import-legacy')->assertSuccessful();

    expect(Page::count())->toBe(0)
        ->and(Page::withTrashed()->sole()->deleted_at)->toEqual(Carbon::parse('2022-05-01 00:00:00'));
});

it('updates pages on reimport instead of duplicating them', function () {
    seedLegacyPage();

    $this->artisan('pages:import-legacy')->assertSuccessful();

    DB::connection('legacy')->table('pages')->where('path', 'help')->update(['title' => 'Aide mise à jour']);

    $this->artisan('pages:import-legacy')->assertSuccessful();

    expect(Page::sole())
        ->title->toBe('Aide mise à jour')
        ->updated_at->toEqual(Carbon::parse('2021-01-01 00:00:00'));
});

it('writes nothing on a dry run', function () {
    seedLegacyPage();

    $this->artisan('pages:import-legacy', ['--dry-run' => true])
        ->expectsOutputToContain('Dry run: nothing was written.')
        ->assertSuccessful();

    expect(Page::withTrashed()->count())->toBe(0);
});

it('counts legacy file references in the report', function () {
    seedLegacyPage([
        'body' => '<!-- wp:image --><figure class="wp-block-image"><img src="https://members.agepac.org/laravel-filemanager/photos/3481/Trombinoscopes/20S.PNG" alt=""/></figure><!-- /wp:image -->',
    ]);

    $this->artisan('pages:import-legacy', ['--dry-run' => true])
        ->expectsOutputToContain('1 legacy file references will be relocated to the private disk')
        ->assertSuccessful();
});

it('relocates legacy files into attachments', function () {
    Storage::fake(Attachment::DISK);
    Http::fake(['members.agepac.org/*' => Http::response($png = fakePng())]);

    seedLegacyPage([
        'body' => '<!-- wp:image --><figure class="wp-block-image"><img src="https://members.agepac.org/laravel-filemanager/photos/3481/Trombinoscopes/EPL 17.PNG" alt=""/></figure><!-- /wp:image -->',
    ]);

    $this->artisan('pages:import-legacy')
        ->expectsOutputToContain('1 legacy file references relocated to the private disk')
        ->assertSuccessful();

    $attachment = Attachment::sole();

    expect($attachment)
        ->id->toBe(Uuid::uuid5(Uuid::NAMESPACE_URL, 'https://members.agepac.org/laravel-filemanager/photos/3481/Trombinoscopes/EPL 17.PNG')->toString())
        ->name->toBe('EPL 17.PNG')
        ->mime_type->toBe('image/png')
        ->size->toBe(strlen($png))
        ->path->toStartWith('pages/attachments/')
        ->path->toEndWith('.png');

    Storage::disk(Attachment::DISK)->assertExists($attachment->path);

    expect(Page::sole()->body)
        ->toContain($attachment->url())
        ->not->toContain('laravel-filemanager');
});

it('rewrites file urls inside html bodies', function () {
    Storage::fake(Attachment::DISK);
    Http::fake(['members.agepac.org/*' => Http::response(fakePng())]);

    seedLegacyPage([
        'body' => "<!-- wp:paragraph {\"align\":\"center\"} -->\n<p style=\"text-align:center\">Centré</p>\n<!-- /wp:paragraph -->\n<!-- wp:image -->\n<figure class=\"wp-block-image\"><img src=\"https://members.agepac.org/laravel-filemanager/photos/3481/Trombinoscopes/EPL 17.PNG\" alt=\"\"/></figure>\n<!-- /wp:image -->",
    ]);

    $this->artisan('pages:import-legacy')->assertSuccessful();

    expect(Page::sole())
        ->format->toBe(PageFormat::HTML)
        ->body->toContain('src="'.Attachment::sole()->url().'"')
        ->body->not->toContain('laravel-filemanager');
});

it('imports each source url once across pages and reruns', function () {
    Storage::fake(Attachment::DISK);
    Http::fake(['members.agepac.org/*' => Http::response(fakePng())]);

    $body = '<!-- wp:image --><figure class="wp-block-image"><img src="https://members.agepac.org/laravel-filemanager/photos/3481/Trombinoscopes/20S.PNG" alt=""/></figure><!-- /wp:image -->';

    seedLegacyPage(['body' => $body]);
    seedLegacyPage(['path' => 'epl20', 'title' => 'EPL20', 'body' => $body]);

    $this->artisan('pages:import-legacy')->assertSuccessful();
    $this->artisan('pages:import-legacy')->assertSuccessful();

    expect(Attachment::count())->toBe(1)
        ->and(Page::query()->pluck('body'))->each->toContain(Attachment::sole()->url());

    Http::assertSentCount(1);
});

it('authenticates downloads with the export token when configured', function () {
    config()->set('services.legacy.export_token', 'secret-token');

    Storage::fake(Attachment::DISK);
    Http::fake(['members.agepac.org/*' => Http::response(fakePng())]);

    seedLegacyPage([
        'body' => '<!-- wp:image --><figure class="wp-block-image"><img src="https://members.agepac.org/laravel-filemanager/photos/3481/Trombinoscopes/20S.PNG" alt=""/></figure><!-- /wp:image -->',
    ]);

    $this->artisan('pages:import-legacy')->assertSuccessful();

    Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer secret-token'));
});

it('does not follow redirects to the legacy login page', function () {
    Storage::fake(Attachment::DISK);
    Http::fake(['members.agepac.org/*' => Http::response('<html>Login</html>', 302, ['Location' => 'https://members.agepac.org/login'])]);

    seedLegacyPage([
        'body' => '<!-- wp:image --><figure class="wp-block-image"><img src="https://members.agepac.org/laravel-filemanager/photos/3481/Trombinoscopes/20S.PNG" alt=""/></figure><!-- /wp:image -->',
    ]);

    $this->artisan('pages:import-legacy')->assertFailed();

    expect(Attachment::count())->toBe(0);
});

it('keeps the legacy url and fails when a download fails', function () {
    Storage::fake(Attachment::DISK);
    Http::fake(['members.agepac.org/*' => Http::response(status: 404)]);

    seedLegacyPage([
        'body' => '<!-- wp:image --><figure class="wp-block-image"><img src="https://members.agepac.org/laravel-filemanager/photos/3481/Trombinoscopes/20S.PNG" alt=""/></figure><!-- /wp:image -->',
    ]);

    $this->artisan('pages:import-legacy')
        ->expectsOutputToContain('1 downloads failed; their legacy URLs were kept.')
        ->assertFailed();

    expect(Attachment::count())->toBe(0)
        ->and(Page::sole()->body)->toContain('https://members.agepac.org/laravel-filemanager/photos/3481/Trombinoscopes/20S.PNG');
});
