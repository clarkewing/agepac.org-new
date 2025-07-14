<?php

namespace App\Livewire\Traits;

use Flux\Flux;
use Illuminate\Support\Js;

trait InteractsWithToasts
{
    use TracksLifecycle;

    protected function toast(
        string $text,
        ?string $heading = null,
        int $duration = 5000,
        ?string $variant = null,
        ?string $position = null
    ): void {
        if ($this->isFirstMount()) {
            $this->callNextTickToast($text, $heading, $duration, $variant, $position);
        } else {
            $this->callFluxToast($text, $heading, $duration, $variant, $position);
        }
    }

    protected function callFluxToast(
        string $text,
        ?string $heading = null,
        int $duration = 5000,
        ?string $variant = null,
        ?string $position = null
    ): void {
        Flux::toast($text, $heading, $duration, $variant, $position);
    }

    protected function callNextTickToast(
        string $text,
        ?string $heading = null,
        int $duration = 5000,
        ?string $variant = null,
        ?string $position = null
    ): void {
        $text = Js::from($text);
        $heading = Js::from($heading);
        $duration = Js::from($duration);
        $variant = Js::from($variant);
        $position = Js::from($position);

        $this->js(<<<JS
            \$nextTick(() => {
                \$flux.toast({
                    text: $text,
                    heading: $heading,
                    duration: $duration,
                    variant: $variant,
                    position: $position,
                })
            })
            JS
        );
    }
}
