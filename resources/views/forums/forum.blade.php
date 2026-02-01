@extends('layouts.app')

@section('title')
    Forum :: {{ $forum->name }}
@endsection

@section('content')
    @if (isset($forum->parent))
        {!! breadcrumbs(['Forum' => 'forum', $forum->parent->name => 'forum/' . $forum->parent->id, $forum->name => 'forum/' . $forum->id]) !!}
    @else
        {!! breadcrumbs(['Forum' => 'forum', $forum->name => 'forum/' . $forum->id]) !!}
    @endif

    <div class="row no-gutters justify-content-between mb-2">
        <div class="col-auto">
            <h1 class="mb-1">
                {!! $forum->displayIcon(50) !!}
                {!! $forum->displayName !!}
            </h1>
        </div>

        @if (!$forum->is_locked || $forum->is_locked && (Auth::check() && Auth::user()->hasPower('manage_forums')))
            <div class="col text-right">
                <a class="btn btn-primary" href="{{ url('forum/' . $forum->id . '/new') }}">
                    New Thread
                </a>
            </div>
        @endif
    </div>

    @if (isset($forum->parent))
        @include('forums._forum_page', ['forum' => $forum, 'posts' => $posts])
    @else
        @include('forums._forum_topper', ['forum' => $forum])
        <hr class="w-75 mx-auto">
        
        <h5 class="text-center mb-3">
            Boards in {!! $forum->name !!}
        </h5>
        @include('forums._category_page', ['forum' => $forum, 'forums' => $forum->children])
    @endif
@endsection
