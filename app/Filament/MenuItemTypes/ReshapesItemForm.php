<?php

namespace App\Filament\MenuItemTypes;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

use function Filament\Support\generate_icon_html;

/**
 * Reshapes the upstream item form for less technical editors: the icon moves
 * out of the Advanced section into its place among the main fields, as a
 * searchable select of outline Heroicons rather than a free-text icon name,
 * and Advanced drops below the custom fields, just above the visibility
 * toggle.
 *
 * Only three upstream facts are relied on, and each degrades gracefully when
 * it stops holding: the first Section is the Advanced section (none found —
 * the form is returned untouched), the icon field lives inside it (absent —
 * nothing is hoisted), and the visibility toggle is named `is_visible`
 * (absent — Advanced goes last).
 */
trait ReshapesItemForm
{
    protected ?Field $hoistedIconField = null;

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        $advanced = null;
        $fields = [];

        foreach (parent::getFormSchema() as $component) {
            if ($advanced === null && $component instanceof Section) {
                $advanced = $component;

                if ($this->hoistedIconField !== null && $this->showsIconField()) {
                    $fields[] = $this->hoistedIconField;
                }

                continue;
            }

            $fields[] = $component;
        }

        if ($advanced === null) {
            return $fields;
        }

        $visibleIndex = count($fields);

        foreach ($fields as $index => $component) {
            if ($component instanceof Field && $component->getName() === 'is_visible') {
                $visibleIndex = $index;
            }
        }

        array_splice($fields, $visibleIndex, 0, [$advanced]);

        return $fields;
    }

    /**
     * Whether the hoisted icon joins the main fields; types whose entries
     * never render an icon drop the field entirely.
     */
    protected function showsIconField(): bool
    {
        return true;
    }

    /**
     * The upstream advanced fields minus the icon, which is swapped for a
     * select under the upstream label. The select stores the same Blade Icons
     * names (e.g. `heroicon-o-home`) the free-text field accepted, so stored
     * icons load straight into it.
     *
     * @return array<Component>
     */
    public function getAdvancedSchema(): array
    {
        $this->hoistedIconField = null;
        $advanced = [];

        foreach (parent::getAdvancedSchema() as $component) {
            if ($component instanceof Field && $component->getName() === 'icon') {
                $this->hoistedIconField = Select::make('icon')
                    ->label($component->getLabel())
                    ->options(fn (): array => static::heroiconOptions())
                    ->searchable()
                    ->allowHtml();

                continue;
            }

            $advanced[] = $component;
        }

        return $advanced;
    }

    /**
     * Filament's outline Heroicons, keyed by Blade Icons name, each labelled
     * with the icon beside its name (e.g. "Arrow right start on rectangle").
     *
     * @return array<string, string>
     */
    protected static function heroiconOptions(): array
    {
        return once(fn (): array => collect(Heroicon::cases())
            ->filter(fn (Heroicon $icon): bool => str_starts_with($icon->value, 'o-'))
            ->mapWithKeys(fn (Heroicon $icon): array => [
                "heroicon-{$icon->value}" => '<span class="flex items-center gap-2">'
                    .generate_icon_html($icon)->toHtml()
                    .e(str($icon->name)->after('Outlined')->headline()->lower()->ucfirst())
                    .'</span>',
            ])
            ->all());
    }
}
