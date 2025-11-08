@extends('layouts.app')

@section('title')
    Forum :: {{ $thread->title }}
@endsection

@section('content')
    {!! breadcrumbs(['Forum' => 'forum', $thread->commentable->name => 'forum/' . $thread->commentable->id, $thread->title => 'forum/' . $thread->commentable->id . '/' . $thread->id]) !!}

    <div class="row no-gutters align-items-center mb-1">
        <h1 class="col-md mb-1">
            {!! $thread->displayName !!}
            <a href="{{ url('reports/new?url=') . $thread->threadUrl }}">
                <i class="fas fa-exclamation-triangle faded" data-toggle="tooltip" title="Click here to report this thread." style="font-size: 0.5em;"></i>
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

    <div class="border rounded mb-2 row no-gutters" style="clear: both;">
        <div class="col-md-2 text-center border-md-right border-bottom border-md-bottom-0 py-2">
            <div class="comment-avatar">
                <img class="mw-100" src="{{ $thread->commenter->avatarUrl }}" style="max-width:100px; max-height:100px; border-radius:50%;" alt="{{ $thread->commenter->name }} Avatar">
            </div>

            <h5 class="mb-1">
                {!! $thread->commenter->displayName !!}
            </h5>
            <p>
                @auth
                    <a href="{{ $thread->commenter->url }}/forum">
                @endauth
                {!! $thread->commenter->forumCount !!} {{ $thread->commenter->forumCount == 1 ? 'Post' : 'Posts' }}
                @auth
                    </a>
                @endauth
            </p>
        </div>

        <div class="col-md">
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
            <div class="p-2">
                {!! config('lorekeeper.settings.wysiwyg_comments') ? nl2br($thread->comment) : nl2br($markdown->line($thread->comment)) !!}
            </div>
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

    @include('forums._form_modals', ['comment' => $thread])

    @if ($replies->count())
        {!! $replies->render() !!}

        @foreach ($replies as $comment)
            @if (!isset($comment->deleted_at))
                <div class="border rounded mb-2 row no-gutters">
                    <div class="col-md-2 text-center border-md-right border-bottom border-md-bottom-0 py-2">
                        <div class="comment-avatar">
                            <img class="mw-100" src="{{ $thread->commenter->avatarUrl }}" style="max-width:100px; max-height:100px; border-radius:50%;" alt="{{ $thread->commenter->name }} Avatar">
                        </div>

                        <h5 class="mb-1">
                            {!! $comment->commenter->displayName !!}
                        </h5>
                        <p>
                            @auth
                                <a href="{{ $thread->commenter->url }}/forum">
                            @endauth
                            {!! $thread->commenter->forumCount !!} {{ $thread->commenter->forumCount == 1 ? 'Post' : 'Posts' }}
                            @auth
                                </a>
                            @endauth
                        </p>
                    </div>

                    <div class="col-md">
                        <div class="mb-2 border-bottom p-2">
                            <div class="row no-gutters justify-content-between">
                                <div class="col">
                                    @if ($comment->type == 'User-User')
                                        <a href="{{ url('comment/') . '/' . $comment->id }}"><i class="fas fa-link ml-1" style="opacity: 50%;"></i></a>
                                    @endif
                                    {!! $comment->created_at->calendar() !!}
                                    @if ($comment->created_at != $comment->updated_at)
                                        <small><span class="text-muted border-left mx-1 px-1">Edited {!! $comment->updated_at->calendar() !!}</span></small>
                                    @endif
                                </div>
                                <div class="col text-right">
                                    @if (Auth::check())
                                        @can('reply-to-comment', $comment)
                                            <a role="button" data-toggle="modal" data-target="#reply-modal-{{ $comment->getKey() }}" class="px-2 py-2 px-sm-2 py-sm-1 text-uppercase" style="cursor: pointer;"><i class="fas fa-comment"></i><span
                                                    class="ml-2 d-none d-sm-inline-block">Reply</span></a>
                                        @endcan
                                        @can('edit-comment', $comment)
                                            <a role="button" data-toggle="modal" data-target="#comment-modal-{{ $comment->getKey() }}" class="px-2 py-2 px-sm-2 py-sm-1 text-uppercase" style="cursor: pointer;"><i class="fas fa-edit"></i><span
                                                    class="ml-2 d-none d-sm-inline-block">Edit</span></a>
                                        @endcan
                                        @can('delete-comment', $comment)
                                            <a role="button" data-toggle="modal" data-target="#delete-modal-{{ $comment->getKey() }}" class="px-2 py-2 px-sm-2 py-sm-1 text-danger text-uppercase" style="cursor: pointer;"><i
                                                    class="fas fa-minus-circle"></i><span class="ml-2 d-none d-sm-inline-block">Delete</span></a>
                                        @endcan
                                        <a href="{{ url('reports/new?url=') . $comment->url }}"><i class="fas fa-exclamation-triangle mr-2" data-toggle="tooltip" title="Click here to report this comment." style="opacity: 50%;"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="p-2">
                            {!! config('lorekeeper.settings.wysiwyg_comments') ? nl2br($comment->comment) : nl2br($markdown->line($comment->comment)) !!}
                        </div>

                        @include('forums._form_modals', ['comment' => $comment])
                    </div>
                </div>
            @endif
        @endforeach

        {!! $replies->render() !!}
    @else
        <div class="text-center text-muted mb-2">
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
            <div class="card p-3">
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
