@if ($changelog)
    {!! Form::open(['url' => 'admin/changelogs/delete/' . $changelog->id]) !!}

    <p>You are about to delete this changelog entry. This is not reversible.</p>
    <p>Are you sure you want to delete this changelog?</p>

    <div class="text-right">
        {!! Form::submit('Delete Changelog', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid changelog selected.
@endif
