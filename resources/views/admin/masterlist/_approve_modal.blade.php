{{ html()->form('POST', 'admin/masterlist/transfer/' . $transfer->id)->open() }}
@if ($transfer->status == 'Accepted')
    <p>This will process the transfer of {!! $transfer->character->displayName !!} from {!! $transfer->sender->displayName !!} to {!! $transfer->recipient->displayName !!} immediately.</p>
@else
    <p>This will approve the transfer of {!! $transfer->character->displayName !!} from {!! $transfer->sender->displayName !!} to {!! $transfer->recipient->displayName !!}, and it will be processed once the recipient accepts it.</p>
@endif
<div class="form-group">
    {{ html()->label('Cooldown (days)', 'cooldown') }}
    {{ html()->text('cooldown', $cooldown)->class('form-control') }}
</div>
<div class="text-right">
    {{ html()->submit('Approve')->class('btn btn-success')->name('action') }}
</div>
{{ html()->form()->close() }}
