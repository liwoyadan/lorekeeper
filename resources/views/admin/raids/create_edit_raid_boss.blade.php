@extends('admin.layout')

@section('admin-title')
    {{ $raidBoss->id ? 'Edit' : 'Create' }} Raid Boss
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Raid Bosses' => 'admin/data/raids/bosses', ($raidBoss->id ? 'Edit' : 'Create') . ' Raid Boss' => $raidBoss->id ? 'admin/data/raids/bosses/edit/' . $raidBoss->id : 'admin/data/raids/bosses/create']) !!}

    <h1>
        {{ $raidBoss->id ? 'Edit' : 'Create' }} Raid Boss
        @if ($raidBoss->id)
            <a href="#" class="btn btn-danger float-right delete-raid-button">Delete Raid Boss</a>
        @endif
    </h1>

    {!! Form::open(['url' => $raidBoss->id ? 'admin/data/raids/bosses/edit/' . $raidBoss->id : 'admin/data/raids/bosses/create/' . $raid->id, 'files' => true]) !!}

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
            Here you can indicate if the color of the remaining health should change once past a certain percentage of damage done.
        </p>
        <div class="text-right mb-2">
            <a class="btn btn-primary" id="addThreshold" href="#" type="button">Add Threshold</a>
        </div>
        <div class="row pb-1">
            <div class="col">
                Health Percentage
            </div>
            <div class="col">
                Color
            </div>
            <div class="col-auto text-right">
                <a href="#" class="btn btn-danger hide"><i class="fas fa-times"></i></a>
            </div>
        </div>
        <div id="thresholdsBody" class="border-top pt-1">
            @if ($raidBoss->thresholds)
                @foreach ($raidBoss->thresholds as $threshold)
                    <div class="row mb-2">
                        <div class="col">
                            <div class="form-group">
                                {!! Form::number('threshold[]', $threshold->amount, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <div class="input-group cp">
                                    {!! Form::text('threshold_color[]', $threshold->color, ['class' => 'form-control']) !!}
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
                @endforeach
            @endif
        </div>
    @endif

    <div class="text-right mt-3">
        {!! Form::submit($raidBoss->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}

    @if ($raidBoss->id)
        <div class="row mb-2 threshold-row hide">
            <div class="col">
                <div class="form-group">
                    {!! Form::number('threshold[]', 0, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col">
                <div class="form-group">
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
    <script>
        $(document).ready(function() {
            $('.delete-raid-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/raids/bosses/delete') }}/{{ $raidBoss->id }}", 'Delete Raid Boss');
            });
            @if ($raidBoss->id)
                var $thresholds = $('#thresholdsBody');
                var $thresholdRow = $('.threshold-row');

                attachRemoveListener($('#thresholdsBody .remove-threshold'));

                $('#addThreshold').on('click', function(e) {
                    e.preventDefault();
                    var $clone = $thresholdRow.clone();
                    $thresholds.append($clone);
                    $clone.removeClass('threshold-row hide');
                    $clone.find('.cp').colorpicker();
                    attachRemoveListener($clone.find('.remove-threshold'));
                });

                function attachRemoveListener(node) {
                    node.on('click', function(e) {
                        e.preventDefault();
                        $(this).parent().parent().remove();
                    });
                }
            @endif
        });
    </script>
@endsection
