@extends('layouts.app')

@section('title')
    Forum :: {{ $thread->title }}
@endsection

@section('content')
    {!! breadcrumbs(['Forum' => 'forum', $thread->commentable->name => 'forum/' . $thread->commentable->id, $thread->title => 'forum/' . $thread->commentable->id . '/' . $thread->id]) !!}

    @if ($thread->commentable->allRules)
        <div class="text-right mb-2">
            @include('forums._rules_modal', ['forum' => $thread->commentable, 'ruleSets' => $thread->commentable->allRules])
        </div>
    @endif
    <div class="row no-gutters align-items-center mb-1">
        <h1 class="col-md mb-1">
            {!! $thread->displayName !!}
            <a href="{{ url('reports/new?url=') . $thread->threadUrl }}">
                <i class="fas fa-exclamation-triangle text-danger" data-toggle="tooltip" title="Click here to report this thread." style="font-size: 0.5em; opacity: 0.5;"></i>
            </a>
        </h1>

        @auth
            @can('reply-to-comment', $thread)
                <div class="col-md text-right">
                    <button data-toggle="modal" data-target="#reply-modal-{{ $thread->getKey() }}" class="btn btn-sm btn-primary text-uppercase">
                        <i class="fas fa-comment"></i>
                        <span class="d-none d-sm-inline-block">
                            Reply to Thread
                        </span>
                    </button>
                </div>
            @endcan
        @endauth
    </div>

    <div class="border rounded mb-2 row no-gutters">
        <div class="col-md-2 text-center border-md-right border-bottom border-md-bottom-0 py-2">
            <h5 class="mb-1">
                {!! $thread->commenter->forumName !!}
            </h5>
            <div class="comment-avatar">
                <img class="mw-100 rounded-circle" src="{{ $thread->character->image->thumbnailUrl ?? $thread->commenter->avatarUrl }}" style="aspect-ratio: 1/1; max-height: 100px;" alt="{{ $thread->character->name ?? $thread->commenter->name }} Avatar">
            </div>

            <h5 class="text-muted mt-1 mb-0">
                @if ($thread->character_id && $thread->character)
                    <span class="small">as {!! $thread->character->displayName !!}</span>
                @endif
            </h5>
            <div>
                {!! $thread->commenter->profile->forumFlair->displayFlair ?? '<span class="small text-muted">(No Forum Flair)</span>' !!}
            </div>
            <div class="small">
                @auth
                    <a href="{{ $thread->commenter->url.'/forum' }}">
                @endauth
                    {!! $thread->commenter->forumCount !!} {{ $thread->commenter->forumCount == 1 ? 'Post' : 'Posts' }}
                @auth
                    </a>
                @endauth
            </div>
        </div>

        <div class="col-md d-flex flex-column">
            <div class="mb-2 border-bottom p-2">
                <div class="row no-gutters justify-content-between">
                    <div class="col">
                        @if ($thread->type == 'User-User')
                            <a href="{{ url('comment/') . '/' . $thread->id }}">
                                <i class="fas fa-link mr-1" style="opacity: 50%;"></i>
                            </a>
                        @endif
                        {!! $thread->created_at->calendar() !!}
                        @if ($thread->created_at != $thread->updated_at)
                            <span class="small text-muted border-left mx-1 px-1">
                                Edited {!! $thread->updated_at->calendar() !!}
                            </span>
                        @endif
                    </div>

                    <div class="col text-right">
                        @if (Auth::check())
                            @can('reply-to-comment', $thread)
                                <a role="button" data-toggle="modal" data-target="#reply-modal-{{ $thread->getKey() }}" class="px-2 py-2 px-sm-2 py-sm-1 text-uppercase" style="cursor: pointer;">
                                    <i class="fas fa-comment"></i>
                                    <span class="ml-2 d-none d-sm-inline-block">Reply</span>
                                </a>
                            @endcan
                            @can('edit-comment', $thread)
                                <a href="{!! $thread->threadUrl . '/edit' !!}" class="px-2 py-2 px-sm-2 py-sm-1 text-uppercase" style="cursor: pointer;">
                                    <i class="fas fa-edit"></i>
                                    <span class="ml-2 d-none d-sm-inline-block">Edit</span>
                                </a>
                            @endcan
                            @can('delete-comment', $thread)
                                <a role="button" data-toggle="modal" data-target="#delete-modal-{{ $thread->getKey() }}" class="px-2 py-2 px-sm-2 py-sm-1 text-danger text-uppercase" style="cursor: pointer;">
                                    <i class="fas fa-minus-circle"></i>
                                    <span class="ml-2 d-none d-sm-inline-block">Delete</span>
                                </a>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="p-2 flex-grow-1 d-flex flex-column">
                <div>
                    {!! config('lorekeeper.settings.wysiwyg_comments') ? nl2br($thread->comment) : nl2br($markdown->line($thread->comment)) !!}
                </div>
            </div>
            @if (config('lorekeeper.forums.allow_signatures.enabled') && (isset($thread->commenter->profile->forum_signature) && $thread->commenter->profile->forum_signature))
                <div class="px-2 pb-2">
                    <hr class="mx-auto my-1" style="width: 90%;">
                    <div class="forum-signature" style="overflow: auto; max-height: {{ config('lorekeeper.forums.allow_signatures.max_height') ?? ''}}px;">
                        {!! $thread->commenter->profile->parsed_forum_signature !!}
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if (Auth::check() && Auth::user()->hasPower('edit_data'))
        <div class="d-flex mb-2 justify-content-end">
            <div class="my-auto mr-1">
                <strong>ADMIN:</strong>
            </div>

            <button data-toggle="modal" data-target="#lock-modal-{{ $thread->getKey() }}" class="btn btn-sm btn-primary mx-1 text-uppercase">
                <i class="fas fa-lock"></i>
                <span class="ml-2 d-none d-sm-inline-block">
                    {{ $thread->is_locked ? 'Unlock' : 'Lock' }} Thread
                </span>
            </button>
            <button data-toggle="modal" data-target="#pin-modal-{{ $thread->getKey() }}" class="btn btn-sm btn-primary mx-1 text-uppercase">
                <i class="fas fa-thumbtack"></i>
                <span class="ml-2 d-none d-sm-inline-block">
                    {{ $thread->is_featured ? 'Unpin' : 'Pin' }} Thread
                </span>
            </button>
        </div>
    @endif

    @include('forums._form_modals', ['comment' => $thread, 'forum' => $thread->commentable])

    @if ($replies->count())
        {!! $replies->render() !!}
        @foreach ($replies as $comment)
            @include('forums._forum_comment', ['comment' => $comment, 'thread' => $thread, 'forum' => $thread->commentable])
        @endforeach
        {!! $replies->render() !!}
    @else
        <div class="text-center text-muted my-2">
            No replies yet.
        </div>
    @endif

    @auth
        @can('reply-to-comment', $thread)
            @include('comments._form', [
                'compact' => isset($type) && $type == 'Staff-Staff' && config('lorekeeper.settings.wysiwyg_comments') ? true : false,
                'model' => $thread->commentable,
                'thread' => $thread,
            ])
        @else
            <div class="card">
                <div class="card-body text-center text-muted">
                    You cannot reply to this thread.
                </div>
            </div>
        @endcan
    @else
        <div class="card my-3">
            <div class="card-body">
                <h5 class="card-title">Authentication required</h5>
                <p class="card-text">You must log in to post a comment.</p>
                <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
            </div>
        </div>
    @endauth
@endsection

@section('scripts')
    @parent
    @include('forums._forum_comment_js')
@endsection
