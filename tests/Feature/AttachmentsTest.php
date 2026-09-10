<?php

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    Storage::fake(Attachment::DISK);

    $this->attachment = Attachment::factory()->create();
});

it('redirects guests to login', function () {
    $this->get(route('attachments.show', $this->attachment))
        ->assertRedirect(route('login'));
});

it('forbids unapproved users from accessing attachments', function () {
    $this->actingAs(User::factory()->unapproved()->create());

    $this->get(route('attachments.show', $this->attachment))
        ->assertForbidden();
});

it('redirects approved users to a temporary file url', function () {
    $this->freezeTime();

    $this->actingAs(User::factory()->create());

    $this->get(route('attachments.show', $this->attachment))
        ->assertRedirect($this->attachment->temporaryUrl());
});

it('keys the stable url by uuid', function () {
    expect(route('attachments.show', $this->attachment, absolute: false))
        ->toBe("/attachments/{$this->attachment->id}");
});

it('ignores the optional decorative filename in the url', function () {
    $this->freezeTime();

    $this->actingAs(User::factory()->create());

    $this->get($this->attachment->url())
        ->assertRedirect($this->attachment->temporaryUrl());

    $this->get(route('attachments.show', ['attachment' => $this->attachment, 'name' => 'anything-else.pdf']))
        ->assertRedirect($this->attachment->temporaryUrl());
});

it('returns a 404 for unknown attachments', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('attachments.show', ['attachment' => Str::uuid7()]))
        ->assertNotFound();
});
