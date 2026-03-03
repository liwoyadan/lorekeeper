@extends('layouts.app')

@section('title')
    Forum :: Create Thread in {{ $forum->name }}
@endsection

@section('content')
    {!! breadcrumbs(['Forum' => 'forum', $forum->name => 'forum/' . $forum->id, 'Create New Thread' => 'forum/' . $forum->id . '/new']) !!}

    <h1 class="mb-1">
        Create Thread in {!! $forum->displayName !!}
    </h1>

    @php
        $model = $forum;
    @endphp

    @auth
        <div class="card mt-3">
            <div class="card-body">
                {!! Form::open(['url' => 'comments/make/' . base64_encode(urlencode(get_class($model))) . '/' . $model->getKey()]) !!}
                <input type="hidden" name="type" value="{{ isset($type) ? $type : null }}" />
                <div class="form-group">
                    {!! Form::label('title', 'Title') !!} {!! add_help('Enter a title relevant to your thread.') !!}
                    {!! Form::text('title', Request::get('title'), ['class' => 'form-control', 'required']) !!}
                </div>
                @if (isset($forum->characters_enabled) && $forum->characters_enabled)
                    <div class="form-group">
                        {!! Form::label('comment_character_id', 'Post as Character (Optional)') !!} {!! add_help('Select a character to post as, or leave blank to post as yourself.') !!}
                        {!! Form::select('comment_character_id', Auth::user()->characters()->visible()->get()->pluck('fullName', 'id')->toArray(), null, ['class' => 'form-control comment-character-select', 'placeholder' => 'Select Character']) !!}
                    </div>
                @endif
                <div class="form-group">
                    {!! Form::label('message', 'Enter your message here:') !!}
                    {!! Form::textarea('message', null, ['class' => 'form-control ' . (config('lorekeeper.settings.wysiwyg_comments') ? 'comment-wysiwyg' : ''), 'rows' => 5, config('lorekeeper.settings.wysiwyg_comments') ? '' : 'required']) !!}
                </div>

                {!! Form::submit('Submit', ['class' => 'btn btn-sm btn-outline-success text-uppercase']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    @else
        <div class="card mt-3">
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
