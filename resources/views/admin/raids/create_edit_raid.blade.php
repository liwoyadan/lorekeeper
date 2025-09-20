@extends('admin.layout')

@section('admin-title')
    {{ $raid->id ? 'Edit' : 'Create' }} Raids
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Raids' => 'admin/data/raids', ($raid->id ? 'Edit' : 'Create') . ' Raid' => $raid->id ? 'admin/data/raids/edit/' . $raid->id : 'admin/data/raids/create']) !!}

    <h1>
        {{ $raid->id ? 'Edit' : 'Create' }} Raid
        @if ($raid->id)
            <a href="#" class="btn btn-danger float-right delete-raid-button">Delete Raid</a>
        @endif
    </h1>

    {!! Form::open(['url' => $raid->id ? 'admin/data/raids/edit/' . $raid->id : 'admin/data/raids/create', 'files' => true]) !!}

    @if (!$raid->id)
        <div class="alert alert-danger">
            First, you must set some basic information for this raid. <b>Once the raid is created</b> you may indicate rewards, bosses, requirements, and toggle visibility on.
        </div>
    @endif

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $raid->name, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Background Image (Optional)') !!} {!! add_help('This image is the raid\'s background that the boss will be placed on top of.') !!}
        <div class="custom-file">
            {!! Form::label('image', 'Choose file...', ['class' => 'custom-file-label']) !!}
            {!! Form::file('image', ['class' => 'custom-file-input']) !!}
        </div>
        <div class="text-muted">Accepted filetypes: png, gif, webp.</div>
        @if ($raid->has_background)
            <div>
                <a href="{{ $raid->imageUrl }}" target="_blank">View current background?</a>
            </div>
            <div class="form-check">
                {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('remove_image', 'Remove current background', ['class' => 'form-check-label']) !!}
            </div>
        @endif
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!} {!! add_help('This is the description of the raid that shows up on the raid page.') !!}
        {!! Form::textarea('description', $raid->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('start_at', 'Start Time (Optional)') !!} {!! add_help('Raids cannot be attacked before the starting time.') !!}
                {!! Form::text('start_at', $raid->start_at, ['class' => 'form-control datepicker']) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('end_at', 'End Time (Optional)') !!} {!! add_help('Raids cannot be attacked after the ending time.') !!}
                {!! Form::text('end_at', $raid->end_at, ['class' => 'form-control datepicker']) !!}
            </div>
        </div>
    </div>

    @if ($raid->id)
        <div class="form-group">
            {!! Form::checkbox('is_visible', 1, $raid->id ? $raid->is_visible : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_visible', 'Is Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Raids that are not visible will be hidden from the raid list. The start/end time hide settings override this setting, i.e. if this is set to visible, it will still be hidden outside of the start/end times.') !!}
        </div>

        <h3>Raid Boss</h3>
        @if (!$raid->bosses->count())
            <p>
                Click the button below to create a boss for this raid.
            </p>
            <a href="{{ url('admin/data/raids/bosses/create/'.$raid->id) }}" class="btn btn-primary d-block mb-3">
                Create Raid Boss
            </a>
        @endif

        <h3>Rewards</h3>
        <p>Rewards are credited on a per-user basis. Mods are able to modify the specific rewards granted at approval time.</p>
        <p>You can add loot tables containing any kind of currencies (both user- and character-attached), but be sure to keep track of which are being distributed! Character-only currencies cannot be given to users.</p>
        @include('widgets._loot_select', ['loots' => $raid->rewards, 'showLootTables' => true, 'showRaffles' => true])
    @endif

    <div class="text-right">
        {!! Form::submit($raid->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}

    @if ($raid->id)
        @include('widgets._loot_select_row', ['showLootTables' => true, 'showRaffles' => true])
        <div class="row mb-2 threshold-row hide">
            <div class="col">
                <div class="form-group">
                    {!! Form::label('Health Percentage') !!}
                    {!! Form::number('threshold[]', 0, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    {!! Form::label('Color') !!}
                    <div class="input-group cp">
                        {!! Form::text('threshold_color[]', null, ['class' => 'form-control']) !!}
                        <span class="input-group-append">
                            <span class="input-group-text colorpicker-input-addon"><i></i></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-auto text-right">
                <a href="#" class="btn btn-danger remove-threshold"><i class="fas fa-times"></i></a>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    @if ($raid->id)
        @include('js._loot_js', ['showLootTables' => true, 'showRaffles' => true])
    @endif
    @include('widgets._datetimepicker_js')
    <script>
        $(document).ready(function() {
            $('.delete-raid-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/raids/delete') }}/{{ $raid->id }}", 'Delete Raid');
            });
        });
    </script>
@endsection
