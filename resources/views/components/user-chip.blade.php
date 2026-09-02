@props([
    'user' => null,
    'sub' => null,
    'size' => null,
    'placeholder' => 'Non renseigné',
])

@if ($user)
    <span {{ $attributes->merge(['class' => 'tk-user-chip']) }}>
        <x-avatar :user="$user" :size="$size" />
        <span class="tk-user-chip__meta">
            <span class="tk-user-chip__name">{{ $user->name }}</span>
            @if ($sub !== false)
                <span class="tk-user-chip__role">{{ $sub ?? $user->job_title ?? $user->role->label() }}</span>
            @endif
        </span>
    </span>
@else
    <span {{ $attributes->merge(['class' => 'tk-user-chip']) }}>
        <span class="tk-avatar {{ $size ? 'tk-avatar-'.$size : '' }} tk-avatar-empty">
            <i class="bi bi-person"></i>
        </span>
        <span class="tk-user-chip__meta">
            <span class="tk-user-chip__role">{{ $placeholder }}</span>
        </span>
    </span>
@endif
