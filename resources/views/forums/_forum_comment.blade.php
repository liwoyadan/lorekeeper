@if (!isset($comment->deleted_at))
    <div class="border rounded mb-3 row no-gutters">
        <div class="col-md-2 text-center border-md-right border-bottom border-md-bottom-0 py-3">
            <div class="comment-avatar">
                <img class="mw-100" src="{{ $thread->commenter->avatarUrl }}" style="max-width:100px; max-height:100px; border-radius:50%;" alt="{{ $thread->commenter->name }} Avatar">
            </div>

            <h5 class="mb-1">
                {!! $comment->commenter->displayName !!}
            </h5>
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

        <div class="col-md">
            <div class="mb-2 border-bottom p-2">
                <div class="row no-gutters justify-content-between">
                    <div class="col d-flex flex-wrap align-items-center">
                        @if ($comment->type == 'User-User')
                            <a href="{{ url('comment/') . '/' . $comment->id }}">
                                <i class="fas fa-link mr-1" style="opacity: 50%;" data-toggle="tooltip" title="Comment Link"></i>
                            </a>
                        @endif
                        {!! $comment->created_at->calendar() !!}
                        @if ($comment->edits->count())
                            <span class="ml-1 border-left h-100"></span>
                            <span class="small text-muted px-1">
                                Edited {!! $comment->updated_at->calendar() !!}
                            </span>
                            @if (Auth::check() && Auth::user()->isStaff)
                                <span class="small text-muted">
                                    [<a href="#" data-toggle="modal" data-target="#show-edits-{{ $comment->id }}">Edit History</a>]
                                </span>
                            @endif
                        @endif
                    </div>

                    @if (Auth::check())
                        <div class="col text-right">
                            @can('edit-comment', $comment)
                                <a role="button" data-toggle="modal" data-target="#comment-modal-{{ $comment->getKey() }}" class="px-2 py-2 p-sm-1 text-uppercase" style="cursor: pointer;">
                                    <i class="fas fa-edit"></i>
                                    <span class="d-none d-sm-inline-block ml-1">Edit</span>
                                </a>
                            @endcan
                            @can('delete-comment', $comment)
                                <a role="button" data-toggle="modal" data-target="#delete-modal-{{ $comment->getKey() }}" class="px-2 py-2 p-sm-1 text-danger text-uppercase" style="cursor: pointer;">
                                    <i class="fas fa-minus-circle"></i>
                                    <span class="d-none d-sm-inline-block ml-1">Delete</span>
                                </a>
                            @endcan
                            <a href="{{ url('reports/new?url=') . $comment->url }}">
                                <i class="fas fa-exclamation-triangle ml-1" data-toggle="tooltip" title="Click here to report this comment." style="opacity: 50%;"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="p-2">
                {!! config('lorekeeper.settings.wysiwyg_comments') ? nl2br($comment->comment) : nl2br($markdown->line($comment->comment)) !!}
            </div>

            @include('forums._form_modals', ['comment' => $comment])
        </div>
    </div>
@endif
