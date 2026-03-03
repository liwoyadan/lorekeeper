@extends('layouts.app')

@section('title')
    Forum :: Create Thread in {{ $forum->name }}
@endsection

@section('content')
    {!! breadcrumbs(['Forum' => 'forum', $forum->name => 'forum/' . $forum->id, 'Edit Thread' => 'forum/' . $forum->id . '/~' . $thread->id . '/edit']) !!}

    <h1 class="mb-1">
        Edit {!! $thread->displayName !!}
    </h1>

    <div class="card">
        <div class="card-body">
            @if ($errors->has('commentable_type'))
                <div class="alert alert-danger" role="alert">
                    {{ $errors->first('commentable_type') }}
                </div>
            @endif
            @if ($errors->has('commentable_id'))
                <div class="alert alert-danger" role="alert">
                    {{ $errors->first('commentable_id') }}
                </div>
            @endif

            {{ Form::model($thread, ['route' => ['comments.update', $thread->getKey()]]) }}
            <h4 class="mb-0">Edit Thread</h4>
            <div>
                <div class="form-group">
                    {!! Form::label('title', 'Title') !!} {!! add_help('Enter a title relevant to your thread.') !!}
                    {!! Form::text('title', $thread->title, ['class' => 'form-control', 'required']) !!}
                </div>
                @if (isset($forum->characters_enabled) && $forum->characters_enabled)
                    <div class="form-group">
                        {!! Form::label('comment_character_id', 'Post as Character (Optional)') !!} {!! add_help('Select a character to post as, or leave blank to post as yourself.') !!}
                        {!! Form::select('comment_character_id', Auth::user()->characters()->visible()->get()->pluck('fullName', 'id')->toArray(), $thread->character_id ?? null, ['class' => 'form-control comment-character-select', 'placeholder' => 'Select Character']) !!}
                    </div>
                @endif
                <div class="form-group">
                    {!! Form::label('message', 'Update your message here:') !!}
                    {!! Form::textarea('message', $thread->comment, ['class' => 'form-control ' . (config('lorekeeper.settings.wysiwyg_comments') ? 'comment-wysiwyg' : ''), 'rows' => 3, config('lorekeeper.settings.wysiwyg_comments') ? '' : 'required']) !!}
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::submit('Update', ['class' => 'btn btn-sm btn-outline-success text-uppercase']) !!}
            </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    @include('forums._forum_comment_js')
@endsection
