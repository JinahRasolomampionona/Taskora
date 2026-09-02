@extends('layouts.app')

@section('title', 'Modifier '.$task->code)

@section('content')
    <x-page-header :title="'Modifier ma demande '.$task->code"
                   subtitle="Votre demande n'a pas encore été prise en charge : vous pouvez la corriger."
                   :breadcrumbs="[
                       'Taskora' => route('client.dashboard'),
                       'Mes demandes' => route('client.tasks.index'),
                       $task->code => route('client.tasks.show', $task),
                       'Modifier' => null,
                   ]" />

    <form method="POST" action="{{ route('client.tasks.update', $task) }}" novalidate>
        @csrf
        @method('PUT')
        @include('client.tasks.partials.form')
    </form>
@endsection
