@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    {{ $character->fullName }}'s Home
@endsection

@section('profile-content')
    {!! breadcrumbs(['Character masterlist' => 'masterlist', $character->fullName => $character->url, 'Home' => $character->url . '/home']) !!}

    @include('character._header', ['character' => $character])

    @if ($home)
        @include('housing._room', ['home' => $home])
        @if ($canEdit)
            @include('housing._editor', ['home' => $home, 'palette' => $palette])
        @endif
    @else
        @if ($canClaim)
            @include('housing._claim', ['action' => 'housing/character/' . $character->id . '/claim', 'intro' => 'This character does not have a home yet.', 'cost' => $claimCost, 'currency' => $claimCurrency])
        @else
            <p>This home has not been set up yet.</p>
        @endif
    @endif
@endsection
