@extends('layouts.app')

@section('title', 'Nouvelle tâche')

@section('content')
    <x-page-header title="Nouvelle tâche"
                   subtitle="Décrivez le travail attendu et distribuez-le à un agent."
                   :breadcrumbs="[
                       'Taskora' => route('admin.dashboard'),
                       'Tâches' => route('admin.tasks.index'),
                       'Nouvelle' => null,
                   ]" />

    <form method="POST" action="{{ route('admin.tasks.store') }}" novalidate>
        @csrf
        @include('admin.tasks.partials.form')
    </form>
@endsection
