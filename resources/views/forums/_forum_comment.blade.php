@if (!isset($comment->deleted_at))
    <div class="{!! isset($postBorderDecor) && $postBorderDecor?->cssStyle ? '' : 'border' !!} rounded mb-3 row no-gutters position-relative" {!! isset($postBorderDecor) && $postBorderDecor?->cssStyle ? 'style="' . $postBorderDecor->cssStyle . '"' : '' !!}>
        @isset($postBgStyle)
            <div class="forum-heading-bg" style="{{ $postBgStyle }}"></div>
        @endisset
        <div class="col-md-2 text-center border-md-right border-bottom border-md-bottom-0 py-2">
            <h5 class="mb-1">
                {!! $comment->commenter->forumName !!}
            </h5>
            <div class="comment-avatar mb-1">
                <img class="mx-100 rounded-circle" src="{{ $comment->character->image->thumbnailUrl ?? $comment->commenter->avatarUrl }}" style="aspect-ratio: 1/1; max-height: 100px;"
                    alt="{{ $comment->character->name ?? $comment->commenter->name }} Avatar">
            </div>
            @if ($comment->character_id && $comment->character)
                <h5 class="mb-0 text-muted">
                    <span class="small">as {!! $comment->character->displayName !!}</span>
                </h5>
            @endif
            <div>
                {!! $comment->commenter->profile->forumFlair->displayFlair ?? '<span class="small text-muted">(No Forum Flair)</span>' !!}
            </div>
            <div>
                {!! $comment->commenter->rank->displayNameIcon ?? '---' !!}
            </div>
            <div class="small mt-1">
                @auth
                    <a href="{{ $comment->commenter->url . '/forum' }}">
                    @endauth
                    {!! $comment->commenter->forumCount !!} {{ $comment->commenter->forumCount == 1 ? 'Post' : 'Posts' }}
                    @auth
                    </a>
                @endauth
            </div>
        </div>

        <div class="col-md d-flex flex-column">
            <div class="border-bottom p-2">
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
            <div class="p-2 flex-grow-1 d-flex flex-column">
                <div>
                    {!! config('lorekeeper.settings.wysiwyg_comments') ? $comment->comment : nl2br($markdown->line($comment->comment)) !!}
                </div>
            </div>
            @if (config('lorekeeper.forums.allow_signatures.enabled') && (isset($comment->commenter->profile->forum_signature) && $comment->commenter->profile->forum_signature))
                <div class="px-2 pb-2">
                    <hr class="mx-auto my-1" style="width: 90%;">
                    <div class="forum-signature" style="overflow: auto; max-height: {{ config('lorekeeper.forums.allow_signatures.max_height') ?? '' }}px;">
                        {!! $comment->commenter->profile->parsed_forum_signature !!}
                    </div>
                </div>
            @endif

            @include('forums._form_modals', ['comment' => $comment, 'forum' => $forum])
        </div>
    </div>
@endif
