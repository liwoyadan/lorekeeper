@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    Transferring {{ $character->fullName }}
@endsection

@section('profile-content')
    @if ($character->is_myo_slot)
        {!! breadcrumbs(['MYO Slot Masterlist' => 'myos', $character->fullName => $character->url, 'Transfer' => $character->url . '/transfer']) !!}
    @else
        {!! breadcrumbs([
            $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
            $character->fullName => $character->url,
            'Transfer' => $character->url . '/transfer',
        ]) !!}
    @endif

    @include('character._header', ['character' => $character])

    @if ($character->user_id == Auth::user()->id)
        <h3>Transfer Character</h3>
        @if (!$character->is_sellable && !$character->is_tradeable && !$character->is_giftable)
            <p>This character cannot be transferred.</p>
        @elseif($character->transferrable_at && $character->transferrable_at->isFuture())
            <p>This character is on transfer cooldown until <strong>{!! format_date($character->transferrable_at) !!}</strong> ({{ $character->transferrable_at->diffForHumans() }}). It cannot be transferred until then.</p>
        @elseif($transfer)
            <div class="card bg-light mb-3">
                <div class="card-body">
                    <p>
                        This character is already in a transfer to {!! $transfer->recipient->displayName !!}.
                    </p>
                    <div class="text-right">
                        {{ html()->form('POST', 'characters/transfer/act/' . $transfer->id)->open() }}
                        {{ html()->submit('Cancel')->class('btn btn-danger')->name('action') }}
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        @elseif($character->trade_id)
            <p>This character is currently attached to a trade. (<a href="{{ $character->trade->url }}">View Trade</a>)</p>
        @else
            <p>
                Transfers require the recipient to confirm that they want to receive the character. Before the recipient makes the confirmation, you may cancel the transfer, but cannot retrieve the character after it has been transferred.
                @if ($transfersQueue)
                    Additionally, a mod will need to approve of the transfer. There may be a wait until the recipient receives the character, even after they have confirmed the transfer.
                @endif
            </p>
            @if ($cooldown)
                <p>
                    After a character is transferred (transfer is accepted{{ $transfersQueue ? ' and approved' : '' }}), a cooldown of <strong>{{ $cooldown }}</strong> days will be applied. During this time, the character cannot be transferred to
                    another person.
                </p>
            @endif
            {{ html()->form('POST', $character->url . '/transfer')->open() }}
            <div class="form-group">
                {{ html()->label('Recipient', 'recipient_id') }}
                {{ html()->select('recipient_id', $userOptions, old('recipient_id'))->class('form-control selectize')->placeholder('Select User') }}
            </div>
            <div class="form-group">
                {{ html()->label('Reason for Transfer (Required)', 'user_reason') }}
                {{ html()->text('user_reason', '')->class('form-control') }}
            </div>
            <div class="text-right">
                {{ html()->submit('Send Transfer')->class('btn btn-primary') }}
            </div>
            {{ html()->form()->close() }}
        @endif
    @endif

    @if (Auth::user()->hasPower('manage_characters'))
        <h3>Admin Transfer</h3>
        <div class="alert alert-warning">
            You are editing this character as a staff member.
        </div>
        <p>This will transfer the character automatically, without requiring the recipient to confirm the transfer. You may also transfer a character that is marked non-transferrable, or still under cooldown. Both the old and new owners will be notified
            of the transfer.</p>
        <p>Fill in either of the recipient fields - if transferring to an off-site user, leave the recipient field blank and vice versa.</p>
        {{ html()->form('POST', $character->is_myo_slot ? 'admin/myo/' . $character->id . '/transfer' : 'admin/character/' . $character->slug . '/transfer')->open() }}
        <div class="form-group">
            {{ html()->label('Recipient', 'recipient_id') }}
            {{ html()->select('recipient_id', $userOptions, old('recipient_id'))->class('form-control selectize')->placeholder('Select User') }}
        </div>
        <div class="form-group">
            {{ html()->label('Recipient Url', 'recipient_url') }} {!! add_help('Characters can only be transferred to offsite user URLs from site(s) used for authentication.') !!}
            {{ html()->text('recipient_url', old('recipient_url'))->class('form-control') }}
        </div>
        <div class="form-group">
            {{ html()->label('Transfer Cooldown (days)', 'cooldown') }}
            {{ html()->text('cooldown', $cooldown)->class('form-control') }}
        </div>
        <div class="form-group">
            {{ html()->label('Reason for Transfer (optional)', 'reason') }}
            {{ html()->text('reason', '')->class('form-control') }}
        </div>
        <div class="text-right">
            {{ html()->submit('Send Transfer')->class('btn btn-primary') }}
        </div>
        {{ html()->form()->close() }}
    @endif

@endsection
@section('scripts')
    @parent
    <script>
        $('.selectize').selectize();
    </script>
@endsection
