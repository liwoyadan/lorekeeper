<div class="card mb-2 zone-row">
    <div class="card-body">
        {!! Form::hidden('zone_id[]', $zone ? $zone->id : '') !!}

        <div class="form-group">
            {!! Form::label('Zone Name') !!}
            {!! Form::text('zone_name[]', $zone ? $zone->name : '', ['class' => 'form-control']) !!}
        </div>

        <div class="form-group zone-mask-field">
            {!! Form::label('Zone Mask (PNG)') !!} {!! add_help('A PNG whose opaque area marks this recolor region. Used in mask render mode.') !!}
            <div class="custom-file">
                {!! Form::label('zone_mask[]', 'Choose file...', ['class' => 'custom-file-label']) !!}
                {!! Form::file('zone_mask[]', ['class' => 'custom-file-input']) !!}
            </div>
            @if ($zone && $zone->has_mask)
                <div class="mt-2"><img src="{{ $zone->maskUrl }}" style="width:48px; height:48px; object-fit:contain;" alt=""></div>
            @endif
        </div>

        <div class="form-group zone-selector-field">
            {!! Form::label('SVG Zone Selector') !!} {!! add_help('A CSS selector matching the recolorable element(s) in the SVG. Used in svg render mode.') !!}
            {!! Form::text('zone_selector[]', $zone ? $zone->svg_selector : '', ['class' => 'form-control', 'placeholder' => '.frame']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('Allow Any Color') !!} {!! add_help('If Yes, the user may pick any color for this zone in addition to the swatches below.') !!}
            {!! Form::select('zone_free_color[]', ['1' => 'Yes', '0' => 'No'], $zone ? $zone->allow_free_color : 1, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('Allowed Patterns') !!} {!! add_help('Library patterns the user may fill this zone with.') !!}
            {!! Form::select('zone_patterns['.$index.'][]', $patterns->pluck('name', 'id'), $zone ? $zone->patterns->pluck('id')->toArray() : [], ['class' => 'form-control zone-patterns', 'multiple']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('Allowed Colors') !!} {!! add_help('Comma-separated hex swatches, for example: b8794a, 3a2a1a, 5a7d9a.') !!}
            {!! Form::text('zone_colors[]', $zone ? $zone->colors->pluck('hex')->implode(', ') : '', ['class' => 'form-control', 'placeholder' => 'b8794a, 3a2a1a']) !!}
        </div>

        <div class="text-right">
            <a href="#" class="btn btn-danger btn-sm remove-zone">Remove Zone</a>
        </div>
    </div>
</div>
