@extends('layouts.app')

@section('title', 'Nouveau projet')

@section('content')
    <x-page-header title="Nouveau projet"
                   subtitle="Définissez un périmètre pour regrouper vos tâches."
                   :breadcrumbs="[
                       'Taskora' => route('admin.dashboard'),
                       'Projets' => route('admin.projects.index'),
                       'Nouveau' => null,
                   ]" />

    <form method="POST" action="{{ route('admin.projects.store') }}" novalidate>
        @csrf
        @include('admin.projects.partials.form')
    </form>
@endsection
