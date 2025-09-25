@extends('admin.layout')

@section('admin-title')
    {{ $raidBoss->id ? 'Edit' : 'Create' }} Raid Boss
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Raid Bosses' => 'admin/data/raid-bosses', ($raidBoss->id ? 'Edit' : 'Create') . ' Raid Boss' => $raidBoss->id ? 'admin/data/raid-bosses/edit/' . $raidBoss->id : 'admin/data/raid-bosses/create']) !!}

    <h1>
        {{ $raidBoss->id ? 'Edit' : 'Create' }} Raid Boss
        @if ($raidBoss->id)
            <a href="#" class="btn btn-danger float-right delete-raid-boss-button m-1">Delete Raid Boss</a>
            <a href="{{ url('admin/data/raids/edit/'.$raidBoss->raid_id) }}" class="btn btn-info float-right m-1">Edit Raid</a>
        @endif
    </h1>

    @if ($raidBoss->id)
        <div class="card text-center mb-3">
            <div class="card-body">
                <h3 class="mb-1">
                    {!! $raidBoss->displayName !!}
                </h3>
                <h5>
                    Belongs to raid {!! $raidBoss->raid->displayName !!}
                </h5>
                @include('widgets.raids._raid_boss_health', ['raidBoss' => $raidBoss])
            </div>
        </div>
    @endif

    {!! Form::open(['url' => $raidBoss->id ? 'admin/data/raid-bosses/edit/' . $raidBoss->id : 'admin/data/raid-bosses/create/' . $raid->id, 'files' => true]) !!}

    @if (!$raidBoss->id)
        <div class="alert alert-danger">
            You are creating a raid boss for <b>{!! $raid->name !!}</b>.
            {!! Form::hidden('raid_id', $raid->id) !!}
        </div>
    @endif

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $raidBoss->name, ['class' => 'form-control']) !!}
    </div>

    @if (!$raidBoss->id)
        <div class="alert alert-danger">
            You will be able to upload images <b>after the boss is first created.</b>
        </div>
    @endif

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!} {!! add_help('This is the description of the raid boss.') !!}
        {!! Form::textarea('description', $raidBoss->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="row">
        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('is_visible', 1, $raidBoss->id ? $raidBoss->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_visible', 'Is Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Raid bosses that are not visible will be hidden from the raid boss list.') !!}
            </div>
        </div>

        <div class="col-md">
            <div class="form-group">
                {!! Form::label('health', 'Health', ['class' => 'form-label']) !!} {!! add_help('The total health the raid boss has.') !!}
                {!! Form::number('health', $raidBoss->id ? $raidBoss->health : null, ['class' => 'form-control', 'placeholder' => 'Enter health...']) !!}
            </div>
        </div>
    </div>

    @if ($raidBoss->id)
        <h3>Health Thresholds</h3>
        <p class="mb-0">
            Here you can indicate if the color of the remaining health bar and text should change based on the boss's current health. <b>You must indicate a color for the bar AND/OR bar's text</b> for the entry to be saved. Type and amount are <b>required</b>.
        </p>
        <div class="text-right mb-2">
            <a class="btn btn-primary" id="addThreshold" href="#" type="button">Add Threshold</a>
        </div>
        <div id="thresholdsBody" class="border-top pt-1">
            @if ($raidBoss->thresholds)
                @foreach ($raidBoss->thresholds as $threshold)
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="text-right">
                                <a href="#" class="btn btn-danger btn-sm remove-threshold">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                            <div class="row mb-1">
                                <div class="col">
                                    <div class="form-group">
                                        {!! Form::label('threshold_type[]', 'Type') !!} {!! add_help('Is this based on percentage or a specified amount?') !!}
                                        {!! Form::select('threshold_type[]', ['percent' => 'Percentage', 'amount' => 'Specific Amount'], $threshold['type'] ?? null, ['class' => 'form-control', 'placeholder' => 'Select type...']) !!}
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        {!! Form::label('threshold_amount[]', 'Amount') !!}
                                        {!! Form::number('threshold_amount[]', $threshold['amount'] ?? null, ['class' => 'form-control', 'placeholder' => 'Enter a number...', 'steps' => '1']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <div class="form-group">
                                        {!! Form::label('threshold_bar_color[]', 'Health Bar Color (Optional)') !!}
                                        <div class="input-group cp">
                                            {!! Form::text('threshold_bar_color[]', $threshold['bar_color'] ?? null, ['class' => 'form-control']) !!}
                                            <span class="input-group-append">
                                                <span class="input-group-text colorpicker-input-addon"><i></i></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group">
                                        {!! Form::label('threshold_text_color[]', 'Health Bar Text Color (Optional)') !!}
                                        <div class="input-group cp">
                                            {!! Form::text('threshold_text_color[]', $threshold['text_color'] ?? null, ['class' => 'form-control']) !!}
                                            <span class="input-group-append">
                                                <span class="input-group-text colorpicker-input-addon"><i></i></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                This threshold will be active when the boss is at
                                @if ($threshold['type'] == 'amount')
                                    <b>{{ $threshold['amount'] }} amount of health</b> or less.
                                @else
                                    <b>{{ $threshold['amount'] > 0 ? $raidBoss->health * ($threshold['amount'] / 100) : '0' }} amount of health ({{ $threshold['amount'] }}%)</b> or less. (Value will be rounded.)
                                @endif
                            </div>
                            @if ($raidBoss->health)
                                <h5 class="text-center mb-0">
                                    Bar Preview
                                </h5>
                                @if ($threshold['type'] == 'amount')
                                    <div class="progress font-weight-bold">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $threshold['amount'] > 0 ? ($threshold['amount'] / $raidBoss->health) * 100 : 0 }}%; {!! isset($threshold['bar_color']) ? 'background-color: '.$threshold['bar_color'].'; ' : '' !!}{!! isset($threshold['text_color']) ? 'color: '.$threshold['text_color'].';' : '' !!}" aria-valuenow="{{ $threshold['amount'] }}" aria-valuemin="0" aria-valuemax="{{ $raidBoss->health }}">
                                            {{ $threshold['amount'] }} / {{ $raidBoss->health }}
                                        </div>
                                    </div>
                                @else
                                    <div class="progress font-weight-bold">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $threshold['amount'] }}%; {!! isset($threshold['bar_color']) ? 'background-color: '.$threshold['bar_color'].'; ' : '' !!}{!! isset($threshold['text_color']) ? 'color: '.$threshold['text_color'].';' : '' !!}" aria-valuenow="{{ $threshold['amount'] > 0 ? $raidBoss->health * ($threshold['amount'] / 100) : '0' }}" aria-valuemin="0" aria-valuemax="{{ $raidBoss->health }}">
                                            {{ $threshold['amount'] > 0 ? $raidBoss->health * ($threshold['amount'] / 100) : '0' }} / {{ $raidBoss->health }}
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @endif

    <div class="text-right mt-3">
        {!! Form::submit($raidBoss->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}

    @if ($raidBoss->id)
        <hr>

        <h3 class="mb-1">
            Boss Images
        </h3>
        <p>
            You may create and edit images for this boss below. You may set a single image, or instead define multiple images that will display based on the boss's current health. Decimal values for health thresholds are always rounded.
        </p>
        <div class="text-right mb-1">
            <a href="#" class="btn btn-sm btn-primary create-boss-image">
                Add New Image
            </a>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                @if (!$bossImages)
                    <p class="text-center text-muted">
                        This boss doesn't have any images yet.
                    </p>
                @else
                    <div class="row no-gutters">
                        @foreach ($bossImages as $image)
                            <div class="col-md-4 p-1 text-center">
                                <div class="card h-100 p-2">
                                    @if ($image->imageUrl)
                                        <div class="mb-2">
                                            <img src="{{ $image->imageUrl }}" class="img-fluid" alt="Image of {{ $raidBoss->name }}">
                                        </div>
                                    @endif
                                    <div class="font-weight-bold">
                                        {{ ucfirst($image->threshold_type) }} - {{ $image->health_threshold ?? 'N/A' }}
                                    </div>
                                    <div class="small">
                                        ({!! $image->thresholdString !!})
                                    </div>
                                    <div>
                                        <a href="#" class="edit-boss-image btn btn-info btn-sm m-1" data-id="{{ $image->id }}">
                                            Edit
                                        </a>
                                        <a href="#" class="delete-boss-image btn btn-danger btn-sm m-1" data-id="{{ $image->id }}">
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 threshold-row hide">
            <div class="card">
                <div class="card-body">
                    <div class="text-right">
                        <a href="#" class="btn btn-danger btn-sm remove-threshold">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                    <div class="row mb-1">
                        <div class="col">
                            <div class="form-group">
                                {!! Form::label('threshold_type[]', 'Type') !!} {!! add_help('Is this based on percentage or a specified amount?') !!}
                                {!! Form::select('threshold_type[]', ['percent' => 'Percentage', 'amount' => 'Specific Amount'], null, ['class' => 'form-control', 'placeholder' => 'Select type...']) !!}
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                {!! Form::label('threshold_amount[]', 'Amount') !!}
                                {!! Form::number('threshold_amount[]', null, ['class' => 'form-control', 'placeholder' => 'Enter a number...', 'steps' => '1']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                {!! Form::label('threshold_bar_color[]', 'Health Bar Color (Optional)') !!}
                                <div class="input-group cp">
                                    {!! Form::text('threshold_bar_color[]', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-append">
                                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                {!! Form::label('threshold_text_color[]', 'Health Bar Text Color (Optional)') !!}
                                <div class="input-group cp">
                                    {!! Form::text('threshold_text_color[]', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-append">
                                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            @if ($raidBoss->id)
                $('.delete-raid-boss-button').on('click', function(e) {
                    e.preventDefault();
                    loadModal("{{ url('admin/data/raid-bosses/delete') }}/{{ $raidBoss->id }}", 'Delete Raid Boss');
                });
                $('.create-boss-image').on('click', function(e) {
                    e.preventDefault();
                    loadModal("{{ url('admin/data/raid-bosses/'.$raidBoss->id.'/image/create') }}", 'Add Boss Image');
                });
                $('.edit-boss-image').on('click', function(e) {
                    e.preventDefault();
                    loadModal("{{ url('admin/data/raid-bosses/'.$raidBoss->id.'/image/edit') }}/" + $(this).data('id'), 'Edit Boss Image');
                });
                $('.delete-boss-image').on('click', function(e) {
                    e.preventDefault();
                    loadModal("{{ url('admin/data/raid-bosses/'.$raidBoss->id.'/image/delete') }}/" + $(this).data('id'), 'Delete Boss Image');
                });

                var $thresholds = $('#thresholdsBody');
                var $thresholdRow = $('.threshold-row');

                attachRemoveListener($('#thresholdsBody .remove-threshold'));

                $('#addThreshold').on('click', function(e) {
                    e.preventDefault();
                    var $clone = $thresholdRow.clone();
                    $thresholds.append($clone);
                    $clone.removeClass('threshold-row hide');
                    $clone.find('.cp').colorpicker();
                    $clone.find('[data-toggle="tooltip"]').tooltip({
                        html: true
                    });
                    attachRemoveListener($clone.find('.remove-threshold'));
                });

                function attachRemoveListener(node) {
                    node.on('click', function(e) {
                        e.preventDefault();
                        $(this).parent().parent().parent().remove();
                    });
                }
            @endif
        });
    </script>
@endsection
