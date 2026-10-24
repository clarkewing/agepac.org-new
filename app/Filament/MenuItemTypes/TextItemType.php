<?php

namespace App\Filament\MenuItemTypes;

use App\Models\MenuItem;
use Datlechin\FilamentMenuBuilder\ItemTypes\TextItemType as BaseTextItemType;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Component;

class TextItemType extends BaseTextItemType
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
            Callout::make(__('admin.nav.empty_group.title'))
                ->warning()
                ->description(__('admin.nav.empty_group.description'))
                ->visible(fn (?MenuItem $record): bool => $record !== null && $record->children()->doesntExist()),
            ...$this->reshapeFormSchema(),
        ];
    }

    /**
     * Groups render as navlist headings, which carry no icon.
     */
    protected function showsIconField(): bool
    {
        return false;
    }
}
