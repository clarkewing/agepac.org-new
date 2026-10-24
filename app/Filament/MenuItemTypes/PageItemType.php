<?php

namespace App\Filament\MenuItemTypes;

use App\Models\Page;
use Datlechin\FilamentMenuBuilder\ItemTypes\ModelItemType;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;

class PageItemType extends ModelItemType
{
    use ReshapesItemForm {
        getFormSchema as reshapeFormSchema;
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        return [
            Callout::make(__('admin.nav.not_live.title'))
                ->warning()
                ->description(__('admin.nav.not_live.description'))
                ->visible(fn (Get $get): bool => static::isNotLive($get('linkable_id'))),
            ...$this->reshapeFormSchema(),
        ];
    }

    /**
     * Whether the selected page will keep the entry hidden: unpublished,
     * trashed, or gone.
     */
    public static function isNotLive(mixed $pageId): bool
    {
        if (blank($pageId)) {
            return false;
        }

        $page = Page::find($pageId);

        return $page === null || ! $page->isPublished();
    }

    /**
     * @return array<Component>
     */
    public function getDestinationSchema(): array
    {
        return [
            $this
                ->makeRecordSelect('linkable_id')
                ->required()
                ->live(),
        ];
    }
}
