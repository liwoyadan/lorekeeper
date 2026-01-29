@include('forums._forum_topper', ['forum' => $forum])

@if ($forum->accessibleSubforums->count())
    <hr class="w-75 mx-auto mb-2">
    <h5 class="mb-1">
        Subforums:
    </h5>
    <div class="row no-gutters mb-2">
        @foreach ($forum->accessibleSubforums as $board)
            <div class="{{ $loop->even ? 'px-md-2' : '' }} py-1 col-md-4">
                <div class="card px-3 py-2 h-100">
                    <div class="row no-gutters">
                        <div class="col font-weight-bold d-flex align-items-center">
                            {!! $board->displayIcon(16) !!}
                            <span class="mx-1">
                                {!! $board->displayName !!}
                            </span>
                            @if (isset($board->description) && $board->description)
                                {!! add_help(strip_tags($board->parsed_description)) !!}
                            @endif
                        </div>
                        <div class="col-auto">
                            {!! $board->comments->whereNull('child_id')->count() !!} Topics
                        </div>
                    </div>
                    @if ($board->accessibleSubforums->count())
                        <div class="small">
                            <b>Sub-Forums:</b>
                            {!! implode(', ', $board->accessibleSubforums->pluck('displayName', 'id')->toArray()) !!}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

<hr class="w-75 mx-auto">

@if ($posts->count())
    <div class="card mb-2">
        <div class="card-body py-1">
            @foreach ($posts as $comment)
                <div class="row no-gutters align-items-center {{ $loop->last ? '' : 'border-bottom' }} py-2">
                    <div class="col-auto pr-2">
                        <i class="fas {{ $comment->is_featured ? 'fa-thumbtack text-primary' : ($comment->is_locked ? 'fa-lock text-muted' : 'fa-circle') }}" data-toggle="tooltip" title="{{ $comment->is_featured ? 'This thread is pinned.' : ($comment->is_locked ? 'This thread is locked.' : 'This thread is open.') }}"></i>
                    </div>
                    <div class="col-11 col-md-6">
                        <div class="font-weight-bold">
                            {!! $comment->displayName !!}
                        </div>
                        <div class="small">
                            by {!! $comment->commenter->displayName !!}, {!! pretty_date($comment->created_at) !!}
                        </div>
                    </div>
                    <div class="col-3 col-md text-center">
                        <p class="mb-0"> {{ $comment->getAllChildren()->count() }} Replies</p>
                    </div>
                    <div class="col pl-2">
                        <div>
                            @if (isset($comment->latestReply))
                                <div>
                                    Latest reply by {!! $comment->latestReply->commenter->displayName !!}, 
                                </div>
                                <div class="small">
                                    {!! pretty_date($comment->latestReply->updated_at) !!}
                                </div>
                            @else
                                <div class="text-muted">
                                    No Replies Yet.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    {!! $posts->render() !!}
@else
    <div class="card">
        <div class="card-body py-3 text-center text-muted">
            No Threads
        </div>
    </div>
@endif
