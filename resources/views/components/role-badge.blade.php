@props(['role'])

@php
    $role = $role instanceof \App\Enums\UserRole
        ? $role
        : \App\Enums\UserRole::from($role);
@endphp

<span {{ $attributes->merge(['class' => 'tk-badge '.$role->badgeClass()]) }}>{{ $role->label() }}</span>
