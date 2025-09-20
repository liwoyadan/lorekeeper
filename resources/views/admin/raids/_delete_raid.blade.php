@if ($raid)
    {!! Form::open(['url' => 'admin/data/raids/delete/' . $raid->id]) !!}

    <p>You are about to delete the raid <strong>{{ $raid->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $raid->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Raid', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid raid selected.
@endif
