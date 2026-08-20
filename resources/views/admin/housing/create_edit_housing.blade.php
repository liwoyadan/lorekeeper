@extends('admin.layout')

@section('admin-title')
    Housing
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Housing' => 'admin/data/housing', ($decor->id ? 'Edit' : 'Create') . ' Decor' => $decor->id ? 'admin/data/housing/edit/' . $decor->id : 'admin/data/housing/create']) !!}

    <h1>{{ $decor->id ? 'Edit' : 'Create' }} Decor
        @if ($decor->id)
            <a href="#" class="btn btn-danger float-right delete-decor-button">Delete Decor</a>
        @endif
    </h1>

    {!! Form::open(['url' => $decor->id ? 'admin/data/housing/edit/' . $decor->id : 'admin/data/housing/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $decor->name, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Kind') !!} {!! add_help('Walls and floors fill fixed room slots. Furniture is placed freely on a layer.') !!}
        {!! Form::select('kind', config('lorekeeper.housing.kinds'), $decor->kind, ['class' => 'form-control', 'id' => 'kind', 'placeholder' => 'Select Kind']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Render Mode') !!} {!! add_help('Mask composites masked layers filled with color or pattern. SVG recolors named elements in a single SVG.') !!}
        {!! Form::select('render_mode', config('lorekeeper.housing.render_modes'), $decor->render_mode ?: 'mask', ['class' => 'form-control']) !!}
    </div>

    <div class="form-group" id="layerGroup">
        {!! Form::label('Layer') !!} {!! add_help('Which layer furniture sits on. Only applies to furniture.') !!}
        {!! Form::select('layer', config('lorekeeper.housing.layers'), $decor->layer, ['class' => 'form-control', 'placeholder' => 'Select Layer']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Default Scale') !!} {!! add_help('Starting size multiplier when first placed. 1 means the art is placed at its natural size.') !!}
        {!! Form::text('default_scale', $decor->default_scale ?: 1, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Base Image (PNG)') !!} {!! add_help('The base art. In mask mode this is the fixed, non-recolored parts. Recolor zones are added after saving.') !!}
        <div class="custom-file">
            {!! Form::label('image', 'Choose file...', ['class' => 'custom-file-label']) !!}
            {!! Form::file('image', ['class' => 'custom-file-input']) !!}
        </div>
        @if ($decor->has_image)
            <div class="mt-2"><img src="{{ $decor->decorImageUrl }}" style="max-width:160px;" alt=""></div>
            <div class="form-check">
                {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
            </div>
        @endif
    </div>

    <div class="form-group">
        {!! Form::checkbox('is_visible', 1, $decor->id ? $decor->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('is_visible', 'Is Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the decor is hidden from the public catalogue.') !!}
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $decor->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    @if ($decor->id)
        <hr>
        <h3>Recolor Zones</h3>
        <p>Each zone is a recolorable region. In mask mode, upload a PNG mask per zone; in svg mode, give a selector. Add allowed patterns and colors, or allow any color. A piece with no zones is not recolorable.</p>

        <div class="text-right mb-3">
            <a href="#" class="btn btn-info" id="addZone">Add Zone</a>
        </div>

        <div id="zoneList">
            @foreach ($decor->zones as $zone)
                @include('admin.housing._zone_row', ['index' => $loop->index, 'zone' => $zone, 'patterns' => $patterns])
            @endforeach
        </div>
    @endif

    <div class="text-right">
        {!! Form::submit($decor->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($decor->id)
        <div id="zonePrototype" class="hide">
            @include('admin.housing._zone_row', ['index' => 0, 'zone' => null, 'patterns' => $patterns])
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            function toggleLayer() {
                if ($('#kind').val() == 'furniture') {
                    $('#layerGroup').show();
                } else {
                    $('#layerGroup').hide();
                }
            }
            toggleLayer();
            $('#kind').on('change', toggleLayer);

            $('.delete-decor-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/housing/delete') }}/{{ $decor->id }}", 'Delete Decor');
            });

            function toggleZoneFields() {
                if ($('select[name=render_mode]').val() == 'mask') {
                    $('.zone-mask-field').show();
                    $('.zone-selector-field').hide();
                } else {
                    $('.zone-mask-field').hide();
                    $('.zone-selector-field').show();
                }
            }
            toggleZoneFields();
            $('select[name=render_mode]').on('change', toggleZoneFields);

            var $zoneProto = $('#zonePrototype').find('.zone-row');

            $('#zoneList .zone-patterns').selectize();
            attachZoneRemove($('#zoneList .remove-zone'));

            $('#addZone').on('click', function(e) {
                e.preventDefault();
                var $clone = $zoneProto.clone();
                $('#zoneList').append($clone);
                $clone.find('.zone-patterns').selectize();
                attachZoneRemove($clone.find('.remove-zone'));
                toggleZoneFields();
            });

            function attachZoneRemove(node) {
                node.on('click', function(e) {
                    e.preventDefault();
                    $(this).closest('.zone-row').remove();
                });
            }

            $('#zoneList').closest('form').on('submit', function() {
                $('#zoneList .zone-row').each(function(index) {
                    $(this).find('.zone-patterns').attr('name', 'zone_patterns[' + index + '][]');
                });
            });
        });
    </script>
@endsection
