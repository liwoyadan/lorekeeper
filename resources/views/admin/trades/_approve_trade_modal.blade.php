{{ html()->form('POST', 'admin/trades/' . $trade->id)->open() }}
<p>This will process the trade between {!! $trade->sender->displayName !!} and {!! $trade->recipient->displayName !!} immediately. Please enter the transfer cooldown period for each character in days (the fields have been pre-filled with the default cooldown value).</p>
@foreach ($trade->getCharacterData() as $character)
    <div class="form-group">
        <label for="cooldowns[{{ $character->id }}]">Cooldown for {!! $character->displayName !!} (Number of Days)</label>
        {{ html()->text('cooldowns[' . $character->id . ']', $cooldown)->class('form-control') }}
    </div>
@endforeach
<div class="text-right">
    {{ html()->submit('Approve')->class('btn btn-success')->name('action') }}
</div>
{{ html()->form()->close() }}
