@if ($setting)
    {!! Form::open(['url' => 'admin/accessibility-settings/delete/' . $setting->id]) !!}
    <p>
        You are about to delete the accessibility/alt setting <b>{{ $setting->name }}</b>. This is not reversible.
    </p>
    <p>
        Users who have saved a value for this setting keep the value stored in their data in the DB, <i>however,</i> it will stop effecting the site's look and no longer appear in the accessibility panel. Recreating a setting on the same property will bring it back.
    </p>
    <p>
        Are you sure you want to delete <b>{{ $setting->name }}</b>?
    </p>
    <div class="text-right">
        {!! Form::submit('Delete Setting', ['class' => 'btn btn-danger']) !!}
    </div>
    {!! Form::close() !!}
@else
    Invalid setting selected.
@endif
