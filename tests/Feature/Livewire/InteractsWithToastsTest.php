<?php

use App\Livewire\Traits\InteractsWithToasts;
use Flux\Flux;
use Livewire\Component;
use Livewire\Livewire;

beforeEach(function () {
    $this->component = new class extends Component {
        use InteractsWithToasts;

        public function mount(): void
        {
            $this->triggerToast('mount');
        }

        public function triggerToast(string $message): void
        {
            $this->toast($message);
        }

        public function render(): string
        {
            return <<<'HTML'
                <div>Toast test</div>
                HTML;
        }
    };
});

it('does not call Flux::toast() on first mount', function () {
    $spy = Flux::spy();

    Livewire::test($this->component);

    $spy->shouldNotHaveReceived('toast');
});

it('calls Flux::toast() on rehydration', function () {
    $spy = Flux::spy();

    $component = Livewire::test($this->component);

    $spy->shouldNotHaveReceived('toast');

    $component->call('triggerToast', 'rehydrate');

    $spy->shouldHaveReceived('toast', ['rehydrate', Mockery::any(), Mockery::any(), Mockery::any(), Mockery::any()]);
});
