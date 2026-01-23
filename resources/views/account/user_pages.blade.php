@extends('account.layout')

@section('account-title')
    User Pages
@endsection

@section('account-content')
    {!! breadcrumbs(['My Account' => Auth::user()->url, 'User Pages' => 'account/user-pages']) !!}

    <h1>
        Your User Pages
    </h1>
    <p>
        Here you may create your own personal pages on the site for uses such as written wishlists, on-site commission pricesheets, personal character information, and more. Content of user pages should comply with the website's <a href="{{ url('info/terms') }}">terms of service</a> - violations of TOS may be removed by staff without warning.<br>
        You may have a maximum of <b>{{ config('lorekeeper.user_pages.page_limit') }} {{ config('lorekeeper.user_pages.page_limit') == 1 ? 'page' : 'pages' }}</b> at any given time.
    </p>

    <div class="text-right mb-2">
        @if (Auth::user()->pages->count() < config('lorekeeper.user_pages.page_limit'))
            <a href="{{ url('account/user-pages/create') }}" class="btn btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i>
                Create Page
            </a>
        @else
            <span data-toggle="tooltip" title="You have reached the maximum number of pages a user is allowed to have at one time.">
                <a href="#" class="btn btn-primary disabled">
                    <i class="fas fa-times" aria-hidden="true"></i>
                    Page Limit Reached
                </a>
            </span>
        @endif
    </div>

    @if (!Auth::user()->pages->count())
        <p class="text-muted text-center">
            You currently have no user pages.
        </p>
    @endif
@endsection

@section('scripts')
    @parent
@endsection
