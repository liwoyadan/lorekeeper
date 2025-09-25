@if ($raidBoss && $bossImage)
    {!! Form::open(['url' => 'admin/data/raid-bosses/' . $raidBoss->id . '/image/delete/' . $bossImage->id]) !!}

    <p>You are about to delete an image for the raid boss <strong>{{ $raidBoss->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete an image for <strong>{{ $raidBoss->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Boss Image', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid boss image selected.
@endif
