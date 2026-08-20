@if ($pattern)
    {!! Form::open(['url' => 'admin/data/housing-patterns/delete/'.$pattern->id]) !!}

    <p>You are about to delete the pattern <strong>{{ $pattern->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $pattern->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Pattern', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid pattern selected.
@endif
