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
                {!! Form::label('start_at', 'Start Time (Optional)') !!} {!! add_help('Raids cannot be attacked before the starting time. If the raid currently is set to not be visible, the raid will <b>automatically become visible</b> once past starting time.') !!}
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
        @if (!$raid->currentBoss())
            <p>
                Click the button below to create a boss for this raid.
            </p>
            <a href="{{ url('admin/data/raid-bosses/create/'.$raid->id) }}" class="btn btn-primary d-block mb-3">
                Create Raid Boss
            </a>
        @else
            @if ($raid->currentBoss())
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <h4 class="mb-1">
                                Current Boss Preview
                            </h4>
                            @include('widgets.raids._raid_boss_display', ['raid' => $raid, 'raidBoss' => $raid->currentBoss()])
                            <a class="btn btn-primary m-1" href="{{ $raid->currentBoss()->adminUrl }}">
                                Edit Boss
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <hr>

        <h3>Damage</h3>
        <p>
            Indicate what deals damage to this raid below. You may indicate a currency or an item, its required quantity per attack, and how much damage an attack deals with an optional range for varied damage.
        </p>
        @include('widgets.raids._raid_damage_select', ['damage' => $raid->damage])

        <hr>

        <h3>Rewards</h3>
        <p>
            Rewards are credited on a per-user basis. Only users who have participated in the raid and have done sufficient enough damage will be rewarded anything indicated below. Rewards are distributed at the end of the raid. The prizes a user receives is <b>inclusive of all the damage thresholds they have passed</b> - i.e. if you reward 5 currency for dealing 20 damage, 10 currency for dealing 30 damage, and 50 currency for dealing 35 damage, a user who has dealt 31 damage by the end of the raid will receive a total of 15 currency.
        </p>
        <p>
            If you want a reward to be distributed to <i>all participating users</i> regardless of the amount of damage they dealt, set the damage requirement to <b>0</b>
        </p>
        @include('widgets.raids._raid_loot_select', ['loots' => $raid->rewards, 'showLootTables' => true, 'showRaffles' => true])
    @endif

    <div class="text-right mt-3">
        {!! Form::submit($raid->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}

    @if ($raid->id)
        @include('widgets.raids._raid_loot_select_row', ['showLootTables' => true, 'showRaffles' => true])
    @endif
@endsection

@section('scripts')
    @parent
    @if ($raid->id)
        @include('js._raid_loot_js', ['showLootTables' => true, 'showRaffles' => true])
    @endif
    @include('widgets._datetimepicker_js')
    <script>
        $(document).ready(function() {
            $('.delete-raid-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/raids/delete') }}/{{ $raid->id }}", 'Delete Raid');
            });

            $('#damageTableBody .selectize').selectize();
            var $damageItemSelect = $('.damage-data').find('.damage-item-select');
            var $damageCurrencySelect = $('.damage-data').find('.damage-currency-select');

            $('.damage-type').on('change', function(e) {
                var val = $(this).val();
                var $cell = $(this).parent().parent().parent().find('.damage-row-select');

                var $clone = null;
                if (val == 'Item') $clone = $damageItemSelect.clone();
                else if (val == 'Currency') $clone = $damageCurrencySelect.clone();

                $cell.html('');
                $cell.append($clone);
                $clone.selectize();
            });
        });
    </script>
@endsection
