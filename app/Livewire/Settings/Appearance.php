<?php

namespace App\Livewire\Settings;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Appearance extends Component
{
    #[Validate]
    public string $language;

    public bool $languageUpdated = false;

    public function mount(): void
    {
        $this->language = session()->get('locale', config('app.locale'));
    }

    public function setLocale(): void
    {
        session()->put('locale', $this->language);
        app()->setLocale($this->language);

        $this->languageUpdated = true;
    }

    protected function rules(): array
    {
        return [
            'language' => ['required', Rule::in(['en', 'fr'])],
        ];
    }

    public function render(): View
    {
        return view('livewire.settings.appearance')
            ->title(__('navigation.settings.appearance').' - '.__('settings.title'));
    }
}
