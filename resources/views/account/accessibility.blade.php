@extends('layouts.app')

@section('title', 'Accessibility')

@section('content')
    {!! breadcrumbs(['Accessibility' => 'accessibility']) !!}
    <h1>
        Accessibility
    </h1>

    @if (!Settings::get('accessibility_menu_enabled'))
        <div class="alert alert-warning">
            The accessibility menu is currently disabled, so it does not appear in the site navigation. You can still adjust your settings here, and any you have saved continue to apply.
        </div>
    @endif

    <p>
        Adjust the site's appearance to suit your needs. Preview your changes, then press Apply to save them.
        @auth
            Your preferences are saved to your user data and follow you across devices and browsers.
        @else
            Your preferences are saved in this browser's localStorage.
            <a href="{{ url('login') }}">Log in</a> to save them to your account and sync across devices and browsers.
        @endauth
    </p>

    @include('account._accessibility_panel')
@endsection
