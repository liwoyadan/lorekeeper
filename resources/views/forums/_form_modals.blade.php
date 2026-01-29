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
            {!! Form::open(['url' => 'comments/' . $comment->id . '/feature']) !!}
            @if (!$comment->is_featured)
                {!! Form::submit('Feature', ['class' => 'btn btn-primary w-100 mb-0 mx-0']) !!}
            @else
                {!! Form::submit('Unfeature', ['class' => 'btn btn-primary w-100 mb-0 mx-0']) !!}
            @endif
            {!! Form::close() !!}
        </div>
    </div>
</div>

@if (Auth::check() && Auth::user()->hasPower('edit_data'))
    <div class="modal fade" id="lock-modal-{{ $thread->getKey() }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $thread->is_featured ? 'Unl' : 'L' }}ock Thread</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">Are you sure you want to {{ $thread->is_featured ? 'un' : '' }}lock this thread?</div>
                    <p>A locked thread cannot be posted in or edited by regular users.</p>
                </div>
                {!! Form::open(['url' => 'comments/' . $thread->id . '/lock']) !!}
                @if (!$thread->is_featured)
                    {!! Form::submit('Lock Thread', ['class' => 'btn btn-primary w-100 mb-0 mx-0']) !!}
                @else
                    {!! Form::submit('Unlock Thread', ['class' => 'btn btn-primary w-100 mb-0 mx-0']) !!}
                @endif
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="pin-modal-{{ $thread->getKey() }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $thread->is_featured ? 'Unp' : 'P' }}in Thread</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">Are you sure you want to {{ $thread->is_featured ? 'un' : '' }}pin this thread?</div>
                    <p>It will be pinned to the top of the {!! $thread->commentable->displayName !!} board.</p>
                </div>
                {!! Form::open(['url' => 'comments/' . $thread->id . '/feature']) !!}
                @if (!$thread->is_featured)
                    {!! Form::submit('Pin Thread', ['class' => 'btn btn-primary w-100 mb-0 mx-0']) !!}
                @else
                    {!! Form::submit('Unpin Thread', ['class' => 'btn btn-primary w-100 mb-0 mx-0']) !!}
                @endif
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endif

{{-- Modals --}}
@can('edit-comment', $comment)
    <div class="modal fade" id="comment-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {{ Form::model($comment, ['route' => ['comments.update', $comment->getKey()]]) }}
                <div class="modal-header">
                    <h5 class="modal-title">Edit Comment</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('message', 'Update your message here:') !!}
                        {!! Form::textarea('message', $comment->comment, ['class' => 'form-control ' . (config('lorekeeper.settings.wysiwyg_comments') ? 'comment-wysiwyg' : ''), 'rows' => 3, config('lorekeeper.settings.wysiwyg_comments') ? '' : 'required']) !!}
                        @if (!config('lorekeeper.settings.wysiwyg_comments'))
                            <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> cheatsheet.</small>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase" data-dismiss="modal">Cancel</button>
                    {!! Form::submit('Update', ['class' => 'btn btn-sm btn-outline-success text-uppercase']) !!}
                </div>
                </form>
            </div>
        </div>
    </div>
@endcan

{{-- modal large --}}
@can('reply-to-comment', $comment)
    <div class="modal fade" id="reply-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {{ Form::open(['route' => ['comments.reply', $comment->getKey()]]) }}
                <div class="modal-header">
                    <h5 class="modal-title">Reply to Comment</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('message', 'Enter your message here:') !!}
                        {!! Form::textarea('message', null, ['class' => 'form-control ' . (config('lorekeeper.settings.wysiwyg_comments') ? 'comment-wysiwyg' : ''), 'rows' => 3, config('lorekeeper.settings.wysiwyg_comments') ? '' : 'required']) !!}
                        @if (!config('lorekeeper.settings.wysiwyg_comments'))
                            <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> cheatsheet.</small>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase" data-dismiss="modal">Cancel</button>
                    {!! Form::submit('Reply', ['class' => 'btn btn-sm btn-outline-success text-uppercase']) !!}
                </div>
                </form>
            </div>
        </div>
    </div>
@endcan

@can('delete-comment', $comment)
    <div class="modal fade" id="delete-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Comment</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">Are you sure you want to delete this comment?</div>
                    <div class="alert alert-warning">
                        <strong>Comments can be restored in the database.</strong>
                        <br> Deleting a comment does not delete the comment record.
                    </div>
                    <div class="text-right">
                        <a href="{{ route('comments.destroy', $comment->getKey()) }}" onclick="event.preventDefault();document.getElementById('comment-delete-form-{{ $comment->getKey() }}').submit();" class="btn btn-danger text-uppercase">Delete</a>
                        <form id="comment-delete-form-{{ $comment->getKey() }}" action="{{ route('comments.destroy', $comment->getKey()) }}" method="POST" style="display: none;">
                            @method('DELETE')
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endcan

<div class="modal fade" id="feature-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
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
            {!! Form::open(['url' => 'comments/' . $comment->id . '/feature']) !!}
            @if (!$comment->is_featured)
                {!! Form::submit('Feature', ['class' => 'btn btn-primary w-100 mb-0 mx-0']) !!}
            @else
                {!! Form::submit('Unfeature', ['class' => 'btn btn-primary w-100 mb-0 mx-0']) !!}
            @endif
            {!! Form::close() !!}
        </div>
    </div>
</div>

{{-- edits modal --}}
{{-- the button for this appears in the main view, but to keep it from being cluttered we will keep the models within this section --}}
@if (Auth::check() && Auth::user()->isStaff)
    <div class="modal fade" id="show-edits-{{ $comment->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit History</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if (count($comment->edits) > 0)
                        <div class="mb-4 logs-table">
                            <div class="logs-table-header">
                                <div class="row">
                                    <div class="col-4 col-md-3">
                                        <div class="logs-table-cell">Time</div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="logs-table-cell">Old Comment</div>
                                    </div>
                                    <div class="col-4 col-md-3">
                                        <div class="logs-table-cell">New Comment</div>
                                    </div>
                                </div>
                            </div>
                            <div class="logs-table-body">
                                @foreach ($comment->edits as $edit)
                                    <div class="logs-table-row">
                                        <div class="row flex-wrap">
                                            <div class="col-4 col-md-3">
                                                <div class="logs-table-cell">
                                                    {!! pretty_date($edit->created_at) !!}
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="logs-table-cell">
                                                    <span data-toggle="tooltip" title="{{ strip_tags($edit->data['old_comment']) }}">
                                                        {!! strip_tags($edit->data['old_comment']) !!}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="logs-table-cell">
                                                    <span data-toggle="tooltip" title="{{ strip_tags($edit->data['new_comment']) }}">
                                                        {{ Str::limit(strip_tags($edit->data['new_comment']), 50) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">No edits yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
