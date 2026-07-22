@extends('layouts.app')

@section('title', 'Accessibility')

@section('content')
    {!! breadcrumbs(['Accessibility' => 'accessibility']) !!}

    <h1>
        Accessibility
    </h1>
    <p>
        Adjust the site's appearance to suit your needs. Changes preview live.
        @auth
            Your preferences are saved to your user data and follow you across devices and browsers.
        @else
            Your preferences are saved in this browser's localStorage. <a href="{{ url('login') }}">Log in</a> to save them to your account and sync across devices and browsers.
        @endauth
    </p>

    @include('account._accessibility_panel')
@endsection
