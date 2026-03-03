<div class="{{ isset($compact) && !$compact ? 'card' : '' }} mt-3">
    <div class="{{ isset($compact) && !$compact ? 'card-body' : '' }}">
        @if (isset($thread) && $thread)
            <h3 class="mb-0">Reply to Thread</h5>
                {!! Form::open(['url' => 'comments/' . $thread->id]) !!}
            @else
                {!! Form::open(['url' => 'comments/make/' . base64_encode(urlencode(get_class($model))) . '/' . $model->getKey()]) !!}
        @endif
        <input type="hidden" name="type" value="{{ isset($type) ? $type : null }}" />
        @if (Auth::check() && isset($thread) && (isset($model->characters_enabled) && $model->characters_enabled))
            <div class="form-group">
                {!! Form::label('comment_character_id', 'Post as Character (Optional)') !!} {!! add_help('Select a character to post as, or leave blank to post as yourself.') !!}
                {!! Form::select('comment_character_id', Auth::user()->characters()->visible()->get()->pluck('fullName', 'id')->toArray(), null, ['class' => 'form-control comment-character-select', 'placeholder' => 'Select Character']) !!}
            </div>
            <script>
                $('.comment-character-select').selectize();
            </script>
        @endif

        <div class="form-group">
            {!! Form::label('message', 'Enter your message here:') !!}
            {!! Form::textarea('message', null, ['class' => 'form-control ' . (config('lorekeeper.settings.wysiwyg_comments') ? 'comment-wysiwyg' : ''), 'rows' => 5, config('lorekeeper.settings.wysiwyg_comments') ? '' : 'required']) !!}
            @if (!config('lorekeeper.settings.wysiwyg_comments'))
                <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> cheatsheet.</small>
            @endif
        </div>

        {!! Form::submit('Submit', ['class' => 'btn btn-sm btn-outline-success text-uppercase']) !!}
        {!! Form::close() !!}
    </div>
</div>
<br />
