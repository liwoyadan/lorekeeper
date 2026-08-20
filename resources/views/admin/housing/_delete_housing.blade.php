@if ($decor)
    {!! Form::open(['url' => 'admin/data/housing/delete/'.$decor->id]) !!}

    <p>You are about to delete the decor <strong>{{ $decor->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $decor->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Decor', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid decor selected.
@endif
