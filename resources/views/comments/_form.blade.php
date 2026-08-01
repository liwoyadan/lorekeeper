<div class="{{ isset($compact) && !$compact ? 'card' : '' }} mt-3">
    <div class="{{ isset($compact) && !$compact ? 'card-body' : '' }}">
        {{ html()->form('POST', 'comments/make/' . base64_encode(urlencode(get_class($model))) . '/' . $model->getKey())->open() }}
        <input type="hidden" name="type" value="{{ isset($type) ? $type : null }}" />
        <div class="form-group">
            {{ html()->label('Enter your message here:', 'message') }}
            {{ html()->textarea('message', null)->class('form-control ' . (config('lorekeeper.settings.wysiwyg_comments') ? 'comment-wysiwyg' : ''))->attribute('rows', 5)->attributeIf(!config('lorekeeper.settings.wysiwyg_comments'), 'required', true) }}
            @if (!config('lorekeeper.settings.wysiwyg_comments'))
                <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> cheatsheet.</small>
            @endif
        </div>

        {{ html()->submit('Submit')->class('btn btn-sm btn-outline-success text-uppercase') }}
        {{ html()->form()->close() }}
    </div>
</div>
<br />
