<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'path',
    'name',
    'mime_type',
    'size',
])]
class Attachment extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The disk holding attachments.
     */
    const string DISK = 'private';

    protected static function booted(): void
    {
        static::deleted(function (Attachment $attachment): void {
            Storage::disk(static::DISK)->delete($attachment->path);
        });
    }

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    /**
     * The stable proxy URL, carrying a decorative filename so consumers that
     * inspect the URL's extension can tell what the file is.
     */
    public function url(): string
    {
        return route('attachments.show', ['attachment' => $this, 'name' => $this->nameSlug()]);
    }

    /**
     * A short-lived signed URL to the file itself, suitable as a redirect
     * target. The stable URL is the `attachments.show` route.
     */
    public function temporaryUrl(): string
    {
        return Storage::disk(static::DISK)->temporaryUrl($this->path, now()->addMinutes(5));
    }

    protected function nameSlug(): string
    {
        $basename = Str::slug(pathinfo($this->name, PATHINFO_FILENAME));
        $extension = strtolower(pathinfo($this->name, PATHINFO_EXTENSION));

        return ($basename !== '' ? $basename : 'file').($extension !== '' ? ".{$extension}" : '');
    }
}
