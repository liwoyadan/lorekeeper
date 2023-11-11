@inject('markdown', 'Parsedown')
@php
    $markdown->setSafeMode(true);
@endphp

@if(isset($reply) && $reply === true)
    <div id="comment-{{ $comment->getKey() }}" class="comment_replies col-12 column mw-100 pr-0 py-2" style="flex-basis: 100%;">
@else
    <div id="comment-{{ $comment->getKey() }}"  class="pt-2" style="flex-basis: 100%;">
@endif

<div class="media-body row no-gutters mx-0" style="flex: 1; flex-wrap: wrap;">
    @if(isset($compact) && !$compact)
        <div class="col-2 pr-2 d-none d-md-flex flex-column justify-content-center align-items-center">
            <img class="img-fluid" src="/images/avatars/{{ $comment->commenter->avatar }}" alt="{{ $comment->commenter->name }} Avatar">
        </div>
    @endif

    <div class="col d-block" style="flex: 1;">
        <div class="row no-gutters mx-0 px-0">
            <div class="col-md-6">
                <h5 class="m-0 col px-0 d-flex flex-column flex-md-row align-items-md-end">
                    {!! $comment->commenter->displayName !!}
                    <span class="text-muted ml-1" style="font-size: 0.75rem;">
                        {!! $comment->commenter->rank->displayName !!} -
                        {!! $comment->created_at !!}
                    </span>
                </h5>
            </div>
            @if($comment->is_featured)
                <div class="text-muted text-right col-md-6 mx-0 pr-1"><small class="text-danger">Featured Comment</small></div>
            @endif
        </div>

        <div class="comment_comment px-3 pt-3 pb-1 {{ $comment->is_featured ? 'featured' : '' }}">
            <p class="comment_content">{!! nl2br($markdown->line($comment->comment)) !!}</p>

            <div class="text-muted text-right" style="font-size: 0.8em;">
                @if($comment->created_at != $comment->updated_at)
                    <span class="text-muted mx-1 px-1">[Edited {!! ($comment->updated_at) !!}]</span>
                @endif
            </div>

            <div class="pt-1 d-flex align-items-center justify-content-end">
                @if($comment->type == "User-User")
                    <a href="{{ url('comment/').'/'.$comment->id }}"><i class="fas fa-link ml-1 text-danger" style="opacity: 50%;"></i></a>
                @endif
                <a href="{{ url('reports/new?url=') . $comment->url }}"><i class="fas fa-exclamation-triangle text-danger" data-toggle="tooltip" title="Click here to report this comment." style="opacity: 50%;"></i></a>

                @if(Auth::check())
                    <span class="mx-1">||</span>

                    @can('edit-comment', $comment)
                        <button data-toggle="modal" data-target="#comment-modal-{{ $comment->getKey() }}" class="btn btn-sm comment_options text-uppercase"><i class="fas fa-edit d-inline-block d-sm-none"></i><span class="d-none d-sm-inline-block">Edit</span></button>
                        <span class="mx-1">|</span>
                    @endcan

                    @if(((Auth::user()->id == $comment->commentable_id) || Auth::user()->isStaff) && (isset($compact) && !$compact))
                        <button data-toggle="modal" data-target="#feature-modal-{{ $comment->getKey() }}" class="btn btn-sm comment_options text-uppercase"><i class="fas fa-star d-inline-block d-sm-none"></i><span class="d-none d-sm-inline-block">{{$comment->is_featured ? 'Unf' : 'F' }}eature Comment</span></button>
                        <span class="mx-1">|</span>
                    @endif

                    @can('delete-comment', $comment)
                        <button data-toggle="modal" data-target="#delete-modal-{{ $comment->getKey() }}" class="btn btn-sm comment_options text-uppercase"><i class="fas fa-minus-circle d-inline-block d-sm-none"></i><span class="d-none d-sm-inline-block">Delete</span></button>
                        <span class="mx-1">|</span>
                    @endcan

                    @can('reply-to-comment', $comment)
                        <button data-toggle="modal" data-target="#reply-modal-{{ $comment->getKey() }}" class="btn btn-sm comment_options text-uppercase"><i class="fas fa-comment d-inline-block d-sm-none"></i><span class="d-none d-sm-inline-block">Reply</span></button>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    {{-- Margin bottom --}}
    {{-- Recursion for children --}}
    <div class="w-100 mw-100">
        @if($grouped_comments->has($comment->getKey()))
            @foreach($grouped_comments[$comment->getKey()] as $child)
                @php $limit++; @endphp

                @if($limit >= 3)
                    <a href="{{ url('comment/').'/'.$comment->id }}"><span class="btn comment_more w-100 my-2"><span class="d-none d-md-inline-block">Click to </span>&nbsp;see more&nbsp;<span class="d-none d-md-inline-block"> of this thread...</span></span></a>
                    @break
                @endif

                @include('comments::_comment', [
                    'comment' => $child,
                    'reply' => true,
                    'grouped_comments' => $grouped_comments
                ])
            @endforeach
        @endif
    </div>
</div>


@can('edit-comment', $comment)
    <div class="modal fade" id="comment-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('comments.update', $comment->getKey()) }}">
                @method('PUT')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Edit Comment</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="message">Update your message here:</label>
                            <textarea required class="form-control" name="message" rows="3">{{ $comment->comment }}</textarea>
                            <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown cheatsheet.</a></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-outline-success text-uppercase">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

@can('reply-to-comment', $comment)
    <div class="modal fade" id="reply-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('comments.reply', $comment->getKey()) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reply to Comment</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="message">Enter your message here:</label>
                            <textarea required class="form-control" name="message" rows="3"></textarea>
                            <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown cheatsheet.</a></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-outline-success text-uppercase">Reply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

@can('delete-comment', $comment)
    <div class="modal fade" id="delete-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Comment</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                    <div class="modal-body">
                        <div class="form-group">Are you sure you want to delete this comment?</div></div>
                        <div class="alert alert-warning"><strong>Comments can be restored in the database.</strong> <br> Deleting a comment does not delete the comment record.</div>
                        <a href="{{ route('comments.destroy', $comment->getKey()) }}" onclick="event.preventDefault();document.getElementById('comment-delete-form-{{ $comment->getKey() }}').submit();" class="btn btn-danger text-uppercase">Delete</a>
                <form id="comment-delete-form-{{ $comment->getKey() }}" action="{{ route('comments.destroy', $comment->getKey()) }}" method="POST" style="display: none;">
                    @method('DELETE')
                    @csrf
                </form>
            </div>
        </div>
    </div>
@endcan

<div class="modal fade" id="feature-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $comment->is_featured ? 'Unf' : 'F' }}eature Comment</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">Are you sure you want to {{ $comment->is_featured ? 'un' : '' }}feature this comment?</div>
            </div>
            <div class="alert alert-warning">Comments can be unfeatured.</div>
                {!! Form::open(['url' => 'comments/'.$comment->id.'/feature']) !!}
                    @if(!$comment->is_featured)
                        {!! Form::submit('Feature', ['class' => 'btn btn-primary w-100 mb-0 mx-0']) !!}
                    @else
                        {!! Form::submit('Unfeature', ['class' => 'btn btn-primary w-100 mb-0 mx-0']) !!}
                    @endif
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
