@if ($raffle->is_active < 2)
    <div class="text-center">
        <p>This will roll {{ $raffle->winner_count }} winner(s) for the raffle <b>{{ $raffle->name }}</b>.</p>
        {{ html()->form('POST', 'admin/raffles/roll/raffle/' . $raffle->id)->open() }}
        {{ html()->submit('Roll!')->class('btn btn-primary') }}
        {{ html()->form()->close() }}
    </div>
@else
    <div class="text-center">This raffle has already been completed.</div>
@endif
