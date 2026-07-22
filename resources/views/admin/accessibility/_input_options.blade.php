@php
    $options = $setting->options_data ?? [];
    $choices = isset($options['choices']) && is_array($options['choices']) ? $options['choices'] : [];
    $presets = isset($options['presets']) && is_array($options['presets']) ? $options['presets'] : [];
@endphp

<div class="a11y-options" data-types="range,number">
    <div class="form-row">
        <div class="form-group col-md-3">
            {!! Form::label('options_data[min]', 'Min') !!}
            {!! Form::number('options_data[min]', $options['min'] ?? null, ['class' => 'form-control', 'step' => 'any']) !!}
        </div>
        <div class="form-group col-md-3">
            {!! Form::label('options_data[max]', 'Max') !!}
            {!! Form::number('options_data[max]', $options['max'] ?? null, ['class' => 'form-control', 'step' => 'any']) !!}
        </div>
        <div class="form-group col-md-3">
            {!! Form::label('options_data[step]', 'Step') !!}
            {!! Form::number('options_data[step]', $options['step'] ?? null, ['class' => 'form-control', 'step' => 'any']) !!}
        </div>
        <div class="form-group col-md-3">
            {!! Form::label('options_data[unit]', 'Unit') !!}
            {!! Form::text('options_data[unit]', $options['unit'] ?? null, ['class' => 'form-control', 'placeholder' => 'i.e. px']) !!}
        </div>
    </div>
    <p class="text-muted small">
        Unit is appended to a bare numerical value (i.e. <code>px</code>). Leave blank for values that lack units, like line-height.
    </p>
</div>

<div class="a11y-options" data-types="select">
    {!! Form::label('options_data[choices]', 'Choices') !!}
    <p class="text-muted small">
        Each choice has a <b>value</b> (the CSS value applied, i.e. a font family) and a <b>label</b> (what the user sees).<br>
        For example: value of <code>'Open Sans', sans-serif</code> is the what will be applied in CSS, and the user will see the label 'Open Sans'.
    </p>
    <div id="choicesList">
        @foreach ($choices as $i => $choice)
            <div class="form-row a11y-choice-row mb-1">
                <div class="col-md-6">
                    {!! Form::text('options_data[choices][' . $i . '][value]', is_array($choice) ? ($choice['value'] ?? '') : $choice, ['class' => 'form-control', 'placeholder' => 'Value']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::text('options_data[choices][' . $i . '][label]', is_array($choice) ? ($choice['label'] ?? '') : '', ['class' => 'form-control', 'placeholder' => 'Label']) !!}
                </div>
                <div class="col-md-1 text-right">
                    <a href="#" class="btn btn-danger a11y-remove-row">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    <a href="#" class="btn btn-outline-primary btn-sm a11y-add-row" data-field="choices">
        <i class="fas fa-plus"></i> Add Choice
    </a>
</div>

<div class="a11y-options" data-types="color">
    {!! Form::label('options_data[presets]', 'Preset Colour Options') !!}
    <p class="text-muted small">
        Used when a colour setting is <b>constrained</b> (users pick a specific colour on a given list). Ignored when free-pick (users get a colour picker instead). Value is a colour; label is optional.
    </p>
    <div id="presetsList">
        @foreach ($presets as $i => $preset)
            <div class="form-row a11y-choice-row mb-1">
                <div class="col-md-6">
                    {!! Form::text('options_data[presets][' . $i . '][value]', is_array($preset) ? ($preset['value'] ?? '') : $preset, ['class' => 'form-control', 'placeholder' => '#000000']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::text('options_data[presets][' . $i . '][label]', is_array($preset) ? ($preset['label'] ?? '') : '', ['class' => 'form-control', 'placeholder' => 'Label (optional)']) !!}
                </div>
                <div class="col-md-1 text-right">
                    <a href="#" class="btn btn-danger a11y-remove-row">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    <a href="#" class="btn btn-outline-primary btn-sm a11y-add-row" data-field="presets">
        <i class="fas fa-plus"></i> Add Swatch
    </a>
</div>

<div class="a11y-options" data-types="toggle">
    <div class="form-row">
        <div class="form-group col-md-6">
            {!! Form::label('options_data[on_value]', 'On Value') !!}
            {!! Form::text('options_data[on_value]', $options['on_value'] ?? null, ['class' => 'form-control', 'placeholder' => 'i.e. contrast(1.25)']) !!}
        </div>
        <div class="form-group col-md-6">
            {!! Form::label('options_data[off_value]', 'Off Value') !!}
            {!! Form::text('options_data[off_value]', $options['off_value'] ?? null, ['class' => 'form-control', 'placeholder' => 'leave blank for none']) !!}
        </div>
    </div>
    <p class="text-muted small">
        Simple on/off toggleable property. Leave blank to fall back to this target's config default.
    </p>
</div>

{{-- the clones...disabled to prevent that form submission issue of hidden form inputs passing thru --}}
<div class="a11y-choice-row a11y-proto form-row mb-1 hide" id="choicesProto">
    <div class="col-md-6">
        {!! Form::text('options_data[choices][__INDEX__][value]', null, ['class' => 'form-control', 'placeholder' => 'Value', 'disabled']) !!}
    </div>
    <div class="col-md-5">
        {!! Form::text('options_data[choices][__INDEX__][label]', null, ['class' => 'form-control', 'placeholder' => 'Label', 'disabled']) !!}
    </div>
    <div class="col-md-1 text-right">
        <a href="#" class="btn btn-danger a11y-remove-row"><i class="fas fa-times"></i></a>
    </div>
</div>
<div class="a11y-choice-row a11y-proto form-row mb-1 hide" id="presetsProto">
    <div class="col-md-6">
        {!! Form::text('options_data[presets][__INDEX__][value]', null, ['class' => 'form-control', 'placeholder' => '#000000', 'disabled']) !!}
    </div>
    <div class="col-md-5">
        {!! Form::text('options_data[presets][__INDEX__][label]', null, ['class' => 'form-control', 'placeholder' => 'Label (optional)', 'disabled']) !!}
    </div>
    <div class="col-md-1 text-right">
        <a href="#" class="btn btn-danger a11y-remove-row"><i class="fas fa-times"></i></a>
    </div>
</div>
