{{--
    A member's initials on a color that stays theirs: it is seeded by the
    user id, so it survives a name change. Defaults to the signed-in user.
--}}

@props([
    'user' => auth()->user(),
    'size' => null,
])

<flux:avatar
    :$size
    :initials="$user->initials()"
    color="auto"
    :color:seed="$user->id"
    {{ $attributes }}
/>
