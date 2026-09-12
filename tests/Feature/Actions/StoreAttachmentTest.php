<?php

use App\Actions\StoreAttachment;
use App\Models\Attachment;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake(Attachment::DISK);
});

it('stores the file on the private disk and records it', function () {
    $file = UploadedFile::fake()->image('foobar.png');

    $attachment = resolve(StoreAttachment::class)($file, 'pages/attachments');

    expect($attachment)->toBeInstanceOf(Attachment::class)
        ->and($attachment->exists)->toBeTrue()
        ->and($attachment->id)->toBeUuid()
        ->and($attachment->path)->toStartWith('pages/attachments/')
        ->and($attachment->name)->toBe('foobar.png')
        ->and($attachment->mime_type)->toBe('image/png')
        ->and($attachment->size)->toBe($file->getSize());

    Storage::disk(Attachment::DISK)->assertExists($attachment->path);
});

it('stores files under hashed names so they cannot be enumerated', function () {
    $file = UploadedFile::fake()->image('foobar.png');

    $attachment = resolve(StoreAttachment::class)($file, 'pages/attachments');

    expect($attachment->path)->not->toContain('foobar');
});

it('records no attachment when the file cannot be stored', function () {
    $disk = Mockery::mock(Filesystem::class);
    $disk->shouldReceive('putFileAs')->andReturn(false);

    Storage::set(Attachment::DISK, $disk);

    $file = UploadedFile::fake()->image('foobar.png');

    expect(fn () => resolve(StoreAttachment::class)($file, 'pages/attachments'))
        ->toThrow(RuntimeException::class);

    expect(Attachment::count())->toBe(0);
});
