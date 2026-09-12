<?php

namespace App\Models;

use App\Enums\NavItemType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'parent_id',
    'order',
    'type',
    'label',
    'page_id',
    'url',
    'icon',
    'badge',
    'badge_color',
    'collapsible',
    'target',
    'disabled',
])]
class NavItem extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => NavItemType::class,
            'label' => 'array',
            'collapsible' => 'boolean',
            'disabled' => 'boolean',
        ];
    }

    /**
     * Keep only filled locale entries, storing null when none remain.
     */
    public function setLabelAttribute(?array $value): void
    {
        $label = array_filter($value ?? [], fn (?string $text): bool => filled($text));

        $this->attributes['label'] = $label === [] ? null : json_encode($label);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id')->orderBy('order');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * The label for the current locale, falling back to any defined locale
     * and then to the linked page's title.
     */
    public function label(): ?string
    {
        return $this->label[app()->getLocale()]
            ?? collect($this->label)->filter()->first()
            ?? $this->page?->title;
    }

    /**
     * The destination this item links to, if any.
     */
    public function linkUrl(): ?string
    {
        return match ($this->type) {
            NavItemType::PAGE => $this->page?->url(),
            NavItemType::URL => $this->url,
            NavItemType::GROUP => null,
        };
    }

    /**
     * Whether the item should render: disabled items are hidden, and a page
     * item vanishes with its page (unpublished, trashed, or deleted).
     */
    public function isVisible(): bool
    {
        if ($this->disabled) {
            return false;
        }

        return $this->type !== NavItemType::PAGE
            || ($this->page !== null && $this->page->isPublished());
    }
}
