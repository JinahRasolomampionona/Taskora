@extends('layouts.app')

@section('title', 'Nouvelle demande')

@section('content')
    <x-page-header title="Nouvelle demande"
                   subtitle="Décrivez votre besoin : nos équipes s'occupent du reste."
                   :breadcrumbs="[
                       'Taskora' => route('client.dashboard'),
                       'Mes demandes' => route('client.tasks.index'),
                       'Nouvelle' => null,
                   ]" />

    <form method="POST" action="{{ route('client.tasks.store') }}" novalidate>
        @csrf
        @include('client.tasks.partials.form')
    </form>
@endsection
