<?php

namespace App\Filament\MenuItemTypes;

use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;

/**
 * Reshapes the upstream item form for less technical editors: the icon moves
 * out of the Advanced section into its place among the main fields, and
 * Advanced drops below the custom fields, just above the visibility toggle.
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
     * The upstream advanced fields minus the icon, which is captured as-is
     * for the main form — label, placeholder, and validation included.
     *
     * @return array<Component>
     */
    public function getAdvancedSchema(): array
    {
        $this->hoistedIconField = null;
        $advanced = [];

        foreach (parent::getAdvancedSchema() as $component) {
            if ($component instanceof Field && $component->getName() === 'icon') {
                $this->hoistedIconField = $component;

                continue;
            }

            $advanced[] = $component;
        }

        return $advanced;
    }
}
