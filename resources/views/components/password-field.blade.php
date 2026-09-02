@props([
    'name' => 'password',
    'id' => null,
    'label' => 'Mot de passe',
    'required' => false,
    'placeholder' => '••••••••',
    'autocomplete' => 'new-password',
    'hint' => null,
])

@php
    $id = $id ?: $name;
    $hasError = $errors->has($name);
@endphp

<label for="{{ $id }}" class="form-label {{ $required ? 'tk-required' : '' }}">{{ $label }}</label>

<div class="tk-password">
    <input type="password" id="{{ $id }}" name="{{ $name }}"
           placeholder="{{ $placeholder }}" autocomplete="{{ $autocomplete }}" @required($required)
           {{ $attributes->merge(['class' => 'form-control'.($hasError ? ' is-invalid' : '')]) }}>

    {{-- Masqué tant que le JavaScript n'a pas pris la main : sans lui, le bouton n'aurait aucun effet. --}}
    <button type="button" class="tk-password__toggle" data-tk-password-toggle aria-controls="{{ $id }}"
            aria-pressed="false" aria-label="Afficher le mot de passe" title="Afficher le mot de passe" hidden>
        <i class="bi bi-eye" aria-hidden="true"></i>
    </button>
</div>

@if ($hint)
    <div class="form-text">{{ $hint }}</div>
@endif

@error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
