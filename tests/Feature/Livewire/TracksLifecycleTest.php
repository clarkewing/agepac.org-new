<?php

use App\Livewire\Traits\TracksLifecycle;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

beforeEach(function () {
    $this->component = new class extends Component {
        use TracksLifecycle;

        public function render(): string
        {
            return <<<'HTML'
                <div>Lifecycle test</div>
                HTML;
        }
    };
});

it('detects first mount', function () {
    Livewire::test($this->component)
        ->tap(fn (Testable $component) => expect($component->invade())
            ->isFirstMount()->toBeTrue()
            ->isRehydrated()->toBeFalse()
        );
});

it('detects rehydration on subsequent updates', function () {
    Livewire::test($this->component)
        ->commit()
        ->tap(fn (Testable $component) => expect($component->invade())
            ->isFirstMount()->toBeFalse()
            ->isRehydrated()->toBeTrue()
        );
});
