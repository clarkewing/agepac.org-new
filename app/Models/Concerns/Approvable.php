<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait Approvable
{
    public function initializeApprovable(): void
    {
        $this->mergeCasts([
            'approved_at' => 'datetime',
        ]);
    }

    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    public function approve(): bool
    {
        return $this->forceFill([
            'approved_at' => now(),
        ])->save();
    }

    #[Scope]
    protected function approved(Builder $query): void
    {
        $query->whereNotNull('approved_at');
    }

    #[Scope]
    protected function unapproved(Builder $query): void
    {
        $query->whereNull('approved_at');
    }
}
