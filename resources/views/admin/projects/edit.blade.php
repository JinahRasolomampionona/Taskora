@extends('layouts.app')

@section('title', 'Modifier '.$project->name)

@section('content')
    <x-page-header :title="'Modifier '.$project->name"
                   :breadcrumbs="[
                       'Taskora' => route('admin.dashboard'),
                       'Projets' => route('admin.projects.index'),
                       $project->code => route('admin.projects.show', $project),
                       'Modifier' => null,
                   ]" />

    <form method="POST" action="{{ route('admin.projects.update', $project) }}" novalidate>
        @csrf
        @method('PUT')
        @include('admin.projects.partials.form')
    </form>
@endsection
