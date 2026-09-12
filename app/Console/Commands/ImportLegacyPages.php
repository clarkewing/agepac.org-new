<?php

namespace App\Console\Commands;

use App\Actions\ImportAttachmentFromUrl;
use App\Enums\PageFormat;
use App\Models\Page;
use App\Services\Content\GutenbergConverter;
use App\Services\Content\HtmlContentDiff;
use App\Services\Content\PageRenderer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ImportLegacyPages extends Command
{
    protected int $failedDownloads = 0;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pages:import-legacy
        {--dry-run : Convert and verify without writing anything}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import legacy Gutenberg pages, converting them to markdown with round-trip verification; pages that would lose content are imported as HTML instead.';

    public function handle(GutenbergConverter $converter, PageRenderer $renderer, HtmlContentDiff $differ): int
    {
        $rows = DB::connection('legacy')->table('pages')->orderBy('id')->get();

        $results = $rows->map(function (object $row) use ($converter, $renderer, $differ) {
            $markdown = $converter->toMarkdown($row->body);

            // The sanitizer drops the Gutenberg block comments when rendering.
            $renderedOriginal = $renderer->renderBody(PageFormat::HTML, $row->body);
            $renderedMarkdown = $renderer->renderBody(PageFormat::MARKDOWN, $markdown);

            $differences = $differ->diff($renderedOriginal, $renderedMarkdown);

            $verdict = match (true) {
                $differences === [] => 'clean',
                $differ->diff($renderedOriginal, $renderedMarkdown, ignoreAlignment: true) === [] => 'alignment-only',
                default => 'lossy',
            };

            return (object) [
                'row' => $row,
                'markdown' => $markdown,
                'format' => $verdict === 'clean' ? PageFormat::MARKDOWN : PageFormat::HTML,
                'verdict' => $verdict,
                'differences' => $differences,
                'legacyFileRefs' => preg_match_all('/laravel-filemanager/', $row->body),
            ];
        });

        foreach ($results as $result) {
            if ($this->output->isVerbose() && $result->differences !== []) {
                $this->warn("Differences for [{$result->row->path}]:");

                foreach ($result->differences as $difference) {
                    $this->line("  {$difference}");
                }
            }

            if (! $this->option('dry-run')) {
                $this->import($result);
            }
        }

        $this->table(
            ['Path', 'Verdict', 'Format', 'Legacy file refs', 'Deleted'],
            $results->map(fn (object $result) => [
                $result->row->path,
                $result->verdict,
                $result->format->value,
                $result->legacyFileRefs ?: '',
                $result->row->deleted_at ? 'yes' : '',
            ]),
        );

        $clean = $results->where('verdict', 'clean')->count();
        $alignmentOnly = $results->where('verdict', 'alignment-only')->count();
        $lossy = $results->where('verdict', 'lossy')->count();
        $fileRefs = $results->sum('legacyFileRefs');

        $this->info(sprintf(
            '%d pages: %d converted cleanly to markdown, %d differ by alignment only, %d lossy (kept as HTML).',
            $results->count(),
            $clean,
            $alignmentOnly,
            $lossy,
        ));

        if ($this->option('dry-run')) {
            $this->info("{$fileRefs} legacy file references will be relocated to the private disk.");
            $this->comment('Dry run: nothing was written.');

            return static::SUCCESS;
        }

        $this->info(sprintf('%d legacy file references relocated to the private disk.', $fileRefs - $this->failedDownloads));

        if ($this->failedDownloads > 0) {
            $this->error("{$this->failedDownloads} downloads failed; their legacy URLs were kept. Fix and rerun.");

            return static::FAILURE;
        }

        return static::SUCCESS;
    }

    protected function import(object $result): void
    {
        $page = Page::withTrashed()->firstOrNew(['path' => $result->row->path]);

        // Encoding the URL attributes of HTML bodies — the converter already
        // does it for markdown — lets legacy file URLs be matched the same
        // way in both formats.
        $body = $result->format === PageFormat::MARKDOWN
            ? $result->markdown
            : GutenbergConverter::encodeUrlAttributes($result->row->body);

        $page->forceFill([
            'title' => $result->row->title,
            'body' => $this->relocateLegacyFiles($body),
            'format' => $result->format,
            'restricted' => (bool) $result->row->restricted,
            'published_at' => $result->row->published_at,
            'created_at' => $result->row->created_at,
            'updated_at' => $result->row->updated_at,
            'deleted_at' => $result->row->deleted_at,
        ]);

        Page::withoutTimestamps(fn () => $page->save());
    }

    /**
     * Replace legacy filemanager URLs with stable attachment proxy URLs,
     * downloading each file into the attachments store. A failed download
     * keeps the legacy URL so the page stays intact until a rerun.
     */
    protected function relocateLegacyFiles(string $body): string
    {
        // URLs are percent-encoded in both formats, so they contain no
        // spaces and end at markdown link syntax, quotes, or whitespace.
        return preg_replace_callback(
            '~https?://'.preg_quote(uri(config('handoff.target_host'))->host(), '~').'/laravel-filemanager/[^\s)"\'<]+~',
            fn (array $matches): string => $this->relocatedUrl($matches[0]),
            $body,
        );
    }

    protected function relocatedUrl(string $url): string
    {
        try {
            return resolve(ImportAttachmentFromUrl::class)($url, 'pages/attachments')->url();
        } catch (RuntimeException $exception) {
            $this->failedDownloads++;

            $this->warn($exception->getMessage());

            return $url;
        }
    }
}
