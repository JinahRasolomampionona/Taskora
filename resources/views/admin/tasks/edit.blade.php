@extends('layouts.app')

@section('title', 'Modifier '.$task->code)

@section('content')
    <x-page-header :title="'Modifier '.$task->code"
                   :subtitle="$task->title"
                   :breadcrumbs="[
                       'Taskora' => route('admin.dashboard'),
                       'Tâches' => route('admin.tasks.index'),
                       $task->code => route('admin.tasks.show', $task),
                       'Modifier' => null,
                   ]" />

    <form method="POST" action="{{ route('admin.tasks.update', $task) }}" novalidate>
        @csrf
        @method('PUT')
        @include('admin.tasks.partials.form')
    </form>
@endsection
