<?php

namespace App\Models;

use App\Enums\PageFormat;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title',
    'path',
    'body',
    'format',
    'restricted',
    'published_at',
])]
class Page extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'format' => PageFormat::class,
            'restricted' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * The page's canonical URL: published public pages live on the public
     * site, everything else (restricted pages, drafts) on squawk.
     */
    public function url(): string
    {
        return ! $this->restricted && $this->isPublished()
            ? route('public.pages.show', $this)
            : route('pages.show', $this);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    public function isScheduled(): bool
    {
        return $this->published_at !== null && $this->published_at->isFuture();
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
