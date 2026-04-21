{{ html()->form('POST', 'admin/masterlist/transfer/' . $transfer->id)->open() }}
<p>This will reject the transfer of {!! $transfer->character->displayName !!} from {!! $transfer->sender->displayName !!} to {!! $transfer->recipient->displayName !!} automatically. The transfer cooldown will not be applied. Are you sure?</p>
<div class="form-group">
    {{ html()->label('Reason for Rejection (optional)', 'reason') }}
    {{ html()->textarea('reason', '')->class('form-control') }}
</div>
<div class="text-right">
    {{ html()->submit('Reject')->class('btn btn-danger')->name('action') }}
</div>
{{ html()->form()->close() }}
