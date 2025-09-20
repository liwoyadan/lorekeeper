@if ($raidBoss)
    {!! Form::open(['url' => 'admin/data/raids/bosses/delete/' . $raidBoss->id]) !!}

    <p>You are about to delete the raid <strong>{{ $raidBoss->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $raidBoss->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Raid', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid raid selected.
@endif
