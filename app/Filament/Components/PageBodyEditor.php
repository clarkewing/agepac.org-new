<?php

namespace App\Filament\Components;

use App\Actions\StoreAttachment;
use App\Models\Attachment;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Support\Js;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * A MarkdownEditor whose file attachments are stored on the private
 * attachments disk and referenced through the stable proxy route, so page
 * bodies never contain storage URLs and files stay members-only.
 */
class PageBodyEditor extends MarkdownEditor
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->fileAttachmentsDisk(Attachment::DISK);

        $this->fileAttachmentsAcceptedFileTypes(['image/png', 'image/jpeg', 'image/gif', 'image/webp', 'application/pdf']);

        $this->saveUploadedFileAttachmentUsing(
            fn (TemporaryUploadedFile $file): Attachment => resolve(StoreAttachment::class)($file, 'pages/attachments'),
        );

        // The filename-carrying URL lets the editor insert images as image
        // markdown and other files (PDFs) as plain links — it decides by the
        // URL's extension.
        $this->getFileAttachmentUrlUsing(
            fn (Attachment $file): string => $file->url(),
        );

        // Two patches applied once the editor has rendered:
        // - The browse-file dialog hardcodes an image-only `accept` attribute
        //   regardless of the accepted file types above, which all three
        //   upload paths (browse, drop, paste) validate against.
        // - The editor's markdown mode does not know front matter, so its
        //   closing `---` styles the YAML above as a giant setext heading.
        //   Front-matter lines get a line class styled as muted metadata.
        $this->extraAlpineAttributes(fn (): array => [
            'x-init' => str_replace(
                '__ACCEPT__',
                Js::from(implode(',', $this->getFileAttachmentsAcceptedFileTypes() ?? []))->toHtml(),
                <<<'JS'
                    $nextTick(() => {
                        const interval = setInterval(() => {
                            const editor = $el._editor
                            const input = $el.querySelector('.imageInput')

                            if (! editor || ! input) return

                            clearInterval(interval)

                            input.accept = __ACCEPT__

                            if (! document.getElementById('page-body-editor-styles')) {
                                const style = document.createElement('style')
                                style.id = 'page-body-editor-styles'
                                style.textContent = `
                                    .CodeMirror .cm-front-matter {
                                        font-family: ui-monospace, monospace;
                                        font-size: 0.8125rem;
                                        font-weight: 400;
                                        opacity: 0.75;
                                    }

                                    /* Neutralize token styling (headings, hr) so every
                                       line reads uniformly; opacity only at line level
                                       to avoid compounding. */
                                    .CodeMirror .cm-front-matter span {
                                        color: inherit;
                                        font: inherit;
                                    }
                                `
                                document.head.append(style)
                            }

                            const cm = editor.codemirror

                            const styleFrontMatter = () => {
                                let end = -1

                                if (cm.getLine(0) === '---') {
                                    for (let i = 1; i < Math.min(cm.lineCount(), 50); i++) {
                                        if (/^---\s*$/.test(cm.getLine(i))) {
                                            end = i
                                            break
                                        }
                                    }
                                }

                                cm.eachLine((line) => {
                                    cm.getLineNumber(line) <= end
                                        ? cm.addLineClass(line, 'text', 'cm-front-matter')
                                        : cm.removeLineClass(line, 'text', 'cm-front-matter')
                                })
                            }

                            cm.on('change', styleFrontMatter)
                            styleFrontMatter()
                        }, 100)

                        setTimeout(() => clearInterval(interval), 5000)
                    })
                    JS,
            ),
        ]);
    }
}
