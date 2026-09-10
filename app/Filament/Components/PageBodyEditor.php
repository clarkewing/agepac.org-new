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

        // The editor's browse-file dialog hardcodes an image-only `accept`
        // attribute regardless of the accepted file types above, which all
        // three upload paths (browse, drop, paste) validate against. Widen
        // the dialog's filter to match once the editor has rendered.
        $this->extraAlpineAttributes(fn (): array => [
            'x-init' => '$nextTick(() => {
                const interval = setInterval(() => {
                    const input = $el.querySelector(\'.imageInput\')
                    if (! input) return
                    input.accept = '.Js::from(implode(',', $this->getFileAttachmentsAcceptedFileTypes() ?? [])).'
                    clearInterval(interval)
                }, 100)
                setTimeout(() => clearInterval(interval), 5000)
            })',
        ]);
    }
}
