<?php

namespace App\Livewire\Traits;

trait TracksLifecycle
{
    protected bool $isRehydrated = false;

    public function isFirstMount(): bool
    {
        return ! $this->isRehydrated;
    }

    public function isRehydrated(): bool
    {
        return $this->isRehydrated;
    }

    public function mountTracksLifecycle(): void
    {
        $this->isRehydrated = false;
    }

    public function hydrateTracksLifecycle(): void
    {
        $this->isRehydrated = true;
    }
}
