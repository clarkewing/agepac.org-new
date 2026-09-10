<?php

use App\Filament\Components\PageBodyEditor;
use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

beforeEach(function () {
    Storage::fake(Attachment::DISK);
});

it('stores attachments on the private disk', function () {
    expect(PageBodyEditor::make('body')->getFileAttachmentsDiskName())
        ->toBe(Attachment::DISK);
});

it('accepts images and pdfs', function () {
    expect(PageBodyEditor::make('body')->getFileAttachmentsAcceptedFileTypes())
        ->toBe(['image/png', 'image/jpeg', 'image/gif', 'image/webp', 'application/pdf']);
});

it('records an uploaded attachment through the store action', function () {
    Storage::fake(FileUploadConfiguration::disk());

    UploadedFile::fake()->image('photo.png')
        ->storeAs(FileUploadConfiguration::directory(), 'photo.png', FileUploadConfiguration::disk());

    $attachment = PageBodyEditor::make('body')->saveUploadedFileAttachment(
        TemporaryUploadedFile::createFromLivewire('photo.png'),
    );

    expect($attachment)->toBeInstanceOf(Attachment::class)
        ->and($attachment->exists)->toBeTrue();

    Storage::disk(Attachment::DISK)->assertExists($attachment->path);
});

it('points attachment urls to the stable proxy route', function () {
    $attachment = Attachment::factory()->create(['name' => 'photo.png']);

    expect(PageBodyEditor::make('body')->getFileAttachmentUrl($attachment))
        ->toBe($attachment->url())
        ->toEndWith("/attachments/{$attachment->id}/photo.png");
});

it('carries the file extension in attachment urls so images insert as images and pdfs as links', function () {
    $attachment = Attachment::factory()->create([
        'name' => 'Super Duper Cool.PDF',
        'mime_type' => 'application/pdf',
    ]);

    expect(PageBodyEditor::make('body')->getFileAttachmentUrl($attachment))
        ->toEndWith("/attachments/{$attachment->id}/super-duper-cool.pdf");
});
