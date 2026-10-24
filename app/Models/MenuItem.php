<?php

namespace App\Models;

use Datlechin\FilamentMenuBuilder\Models\MenuItem as BaseMenuItem;

class MenuItem extends BaseMenuItem
{
    /**
     * An unpublished page is a dead destination: resolving no URL prunes the
     * item from rendered menus, exactly like a trashed or deleted page.
     */
    public function getUrl(?string $locale = null): ?string
    {
        if ($this->linkable instanceof Page && ! $this->linkable->isPublished()) {
            return null;
        }

        return parent::getUrl($locale);
    }

    /**
     * A childless group heading leads nowhere: claiming a destination that
     * resolves to no URL makes upstream prune it from rendered menus and
     * flag it in the builder, exactly like a broken link.
     */
    public function hasDestination(): bool
    {
        if ($this->isGroupHeading()) {
            return $this->children->isEmpty();
        }

        return parent::hasDestination();
    }

    public function isGroupHeading(): bool
    {
        return $this->type === 'text';
    }
}
