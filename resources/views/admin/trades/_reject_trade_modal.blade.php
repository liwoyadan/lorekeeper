{{ html()->form('POST', 'admin/trades/' . $trade->id)->open() }}
<p>This will reject the trade between {!! $trade->sender->displayName !!} and {!! $trade->recipient->displayName !!} automatically, returning all items/currency/characters to their owners. The character transfer cooldown will not be applied. Are you sure?</p>
<div class="form-group">
    {{ html()->label('Reason for Rejection (optional)', 'reason') }}
    {{ html()->textarea('reason', '')->class('form-control') }}
</div>
<div class="text-right">
    {{ html()->submit('Reject')->class('btn btn-danger')->name('action') }}
</div>
{{ html()->form()->close() }}
