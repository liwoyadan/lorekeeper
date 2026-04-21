<p>
    All information entered into a bookmark is strictly private - the owner of the bookmarked character will not be notified, and will not know who/how many users have bookmarked their character.
</p>
{{ html()->form('POST', $bookmark->id ? 'account/bookmarks/edit/' . $bookmark->id : 'account/bookmarks/create')->open() }}
{{ html()->hidden('character_id', Request::get('character_id')) }}
<div class="form-group">
    {{ html()->label('Notify me when...', 'notify') }} {!! add_help('This will notify you whenever the respective change occurs, and is entirely optional.') !!}
    <div class="form-check">
        {{ html()->checkbox('notify_on_trade_status', $bookmark->notify_on_trade_status, 1)->class('form-check-input')->id('notifyTrade') }}
        {{ html()->label('Open For Trades status changes', 'notifyTrade')->class('form-check-label') }}
    </div>
    <div class="form-check">
        {{ html()->checkbox('notify_on_gift_art_status', $bookmark->notify_on_gift_art_status, 1)->class('form-check-input')->id('notifyGiftArt') }}
        {{ html()->label('Gift Art Allowed status changes', 'notifyGiftArt')->class('form-check-label') }}
    </div>
    <div class="form-check">
        {{ html()->checkbox('notify_on_gift_writing_status', $bookmark->notify_on_gift_writing_status, 1)->class('form-check-input')->id('notifyGiftArt') }}
        {{ html()->label('Gift Writing Allowed status changes', 'notifyGiftWriting')->class('form-check-label') }}
    </div>
    <div class="form-check">
        {{ html()->checkbox('notify_on_transfer', $bookmark->notify_on_transfer, 1)->class('form-check-input')->id('notifyTransfer') }}
        {{ html()->label('Character\'s owner changes', 'notifyTransfer')->class('form-check-label') }}
    </div>
    <div class="form-check">
        {{ html()->checkbox('notify_on_image', $bookmark->notify_on_image, 1)->class('form-check-input')->id('notifyImage') }}
        {{ html()->label('A new image is uploaded', 'notifyImage')->class('form-check-label') }}
    </div>
</div>
<div class="form-group">
    {{ html()->label('Comment (Optional)', 'comment') }} {!! add_help('HTML will not be rendered. Newlines will be honoured.') !!}
    {{ html()->textarea('comment', $bookmark->comment)->class('form-control')->attribute('maxLength', 500) }}
</div>
<div class="text-right">
    {{ html()->submit('Submit')->class('btn btn-primary') }}
</div>
{{ html()->form()->close() }}
