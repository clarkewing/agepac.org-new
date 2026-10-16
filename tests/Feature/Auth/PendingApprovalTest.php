<?php

use App\Models\User;

it('tells a member pending approval what happens next', function () {
    $user = User::factory()->pendingApproval()->create();

    $this->actingAs($user)
        ->get(route('pending-approval'))
        ->assertSeeLivewire('pages::auth.pending-approval')
        ->assertSeeHtml('<title>'.__('auth.pending-approval.title').' · '.config('app.name').'</title>')
        ->assertSee(__('auth.pending-approval.steps.verified'))
        ->assertSee($user->email)
        ->assertSee(__('auth.pending-approval.steps.approval-hint'));
});

it('escapes the member’s email address', function () {
    $this->actingAs(User::factory()->pendingApproval()->create(['email' => '"<b>bold</b>"@example.com']))
        ->get(route('pending-approval'))
        ->assertSee('&lt;b&gt;bold&lt;/b&gt;', escape: false)
        ->assertDontSee('<b>bold</b>', escape: false);
});

it('offers to verify an email address that is not verified yet', function () {
    $this->actingAs(User::factory()->pendingApproval()->unverified()->create())
        ->get(route('pending-approval'))
        ->assertSee(__('auth.pending-approval.steps.verify'))
        ->assertDontSee(__('auth.pending-approval.steps.verified'));
});

it('sends an approved member on to the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('pending-approval'))
        ->assertRedirect(route('dashboard'));
});

it('redirects guests to the login page', function () {
    $this->get(route('pending-approval'))
        ->assertRedirect(route('login'));
});
