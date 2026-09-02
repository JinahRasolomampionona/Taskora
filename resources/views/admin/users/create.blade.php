@extends('layouts.app')

@section('title', 'Nouveau compte')

@section('content')
    <x-page-header title="Nouveau compte"
                   subtitle="Créez un accès agent, client ou administrateur."
                   :breadcrumbs="[
                       'Taskora' => route('admin.dashboard'),
                       'Utilisateurs' => route('admin.users.index'),
                       'Nouveau' => null,
                   ]" />

    <form method="POST" action="{{ route('admin.users.store') }}" novalidate>
        @csrf
        @include('admin.users.partials.form')
    </form>
@endsection
