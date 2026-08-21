@extends('user.layout', ['user' => $user])

@section('profile-title')
    {{ $user->name }}'s Home
@endsection

@section('profile-content')
    {!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Home' => $user->url . '/home']) !!}

    <h1>{{ $user->name }}'s Home</h1>

    @if ($home)
        @include('housing._room', ['home' => $home])
        @if ($canEdit)
            @include('housing._editor', ['home' => $home, 'palette' => $palette])
        @endif
    @else
        @if ($canClaim)
            @include('housing._claim', ['action' => 'housing/claim', 'intro' => 'This home has not been claimed yet.', 'cost' => $claimCost, 'currency' => $claimCurrency])
        @else
            <p>This home has not been set up yet.</p>
        @endif
    @endif
@endsection
