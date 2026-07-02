@if ($bootstrap)
    @if ($bootstrap->themes_count > 0)
        <p>
            <b>{{ $bootstrap->name }}</b> cannot be deleted because it is currently in use by {{ $bootstrap->themes_count }} {{ $bootstrap->themes_count == 1 ? 'theme' : 'themes' }}.
            Remove this Bootstrap theme from all themes in order to delete it.
        </p>
    @else
        {!! Form::open(['url' => 'admin/bootstrap-themes/delete/' . $bootstrap->id]) !!}
        <p>
            You are about to delete the Bootstrap theme <b>{{ $bootstrap->name }}</b>. This is not reversible.
        </p>
        <p>Are you sure you want to delete <b>{{ $bootstrap->name }}</b>?</p>
        <div class="text-right">
            {!! Form::submit('Delete Bootstrap Theme', ['class' => 'btn btn-danger']) !!}
        </div>
        {!! Form::close() !!}
    @endif
@else
    Invalid Bootstrap theme selected.
@endif
