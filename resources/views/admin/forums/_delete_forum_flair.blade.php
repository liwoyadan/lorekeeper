@if ($flair)
    {!! Form::open(['url' => 'admin/forum-flairs/delete/' . $flair->id]) !!}

    <p>You are about to delete the flair <strong>{{ $flair->name }}</strong>. This is not reversible.</p>
    <p>Users who currently have this flair equipped will have it removed.</p>
    <p>Are you sure you want to delete <strong>{{ $flair->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Flair', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid flair selected.
@endif
