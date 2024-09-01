@extends('user.layout')

@section('profile-title')
    {{ $user->name }}'s Mignyans
@endsection

@section('profile-content')
    {!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Mignyans' => $user->url . '/characters']) !!}

    <h1>
        {!! $user->displayName !!}'s Mignyans
    </h1>

    @include('user._characters', ['characters' => $characters, 'myo' => false])
@endsection
