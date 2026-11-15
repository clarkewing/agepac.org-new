<?php

use App\Models\User;

it('redirects the settings root to the profile page by absolute URL', function () {
    $this->actingAs(User::factory()->create())
        ->get('/settings/')
        ->assertRedirectToRoute('settings.profile')
        ->assertHeader('Location', route('settings.profile'));
});
