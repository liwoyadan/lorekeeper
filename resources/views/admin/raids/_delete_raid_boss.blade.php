@if ($raidBoss)
    {!! Form::open(['url' => 'admin/data/raid-bosses/delete/' . $raidBoss->id]) !!}

    <p>You are about to delete the raid boss <strong>{{ $raidBoss->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $raidBoss->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Raid Boss', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid raid boss selected.
@endif
