<?php

namespace App\Livewire\Settings;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Appearance extends Component
{
    #[Validate]
    public string $language;

    public function mount(): void
    {
        $this->language = session()->get('locale', config('app.locale'));
    }

    public function setLocale(): void
    {
        session()->put('locale', $this->language);

        app()->setLocale($this->language);
    }

    protected function rules(): array
    {
        return [
            'language' => ['required', Rule::in(['en', 'fr'])],
        ];
    }
}
