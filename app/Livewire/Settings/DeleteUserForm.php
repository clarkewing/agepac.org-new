<?php

namespace App\Livewire\Settings;

use App\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeleteUserForm extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        session()->flash('status', __('settings.profile.delete-account.status.deleted'));

        // TODO: Add navigate: true
        $this->redirectRoute('login');
    }
}
