@extends('layouts.app')

@section('title', 'Modifier '.$user->name)

@section('content')
    <x-page-header :title="'Modifier '.$user->name"
                   :breadcrumbs="[
                       'Taskora' => route('admin.dashboard'),
                       'Utilisateurs' => route('admin.users.index'),
                       $user->name => route('admin.users.show', $user),
                       'Modifier' => null,
                   ]" />

    <form method="POST" action="{{ route('admin.users.update', $user) }}" novalidate>
        @csrf
        @method('PUT')
        @include('admin.users.partials.form')
    </form>
@endsection
