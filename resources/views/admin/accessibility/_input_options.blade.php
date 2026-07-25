@php
    $options = $setting->options_data ?? [];
    $choices = isset($options['choices']) && is_array($options['choices']) ? $options['choices'] : [];
    $presets = isset($options['presets']) && is_array($options['presets']) ? $options['presets'] : [];
@endphp

{{-- RANGE --}}
<div class="a11y-options" data-types="range,number">
    <div class="form-row">
        <div class="form-group col-6 col-md-3">
            {!! Form::label('options_data[min]', 'Min') !!}
            {!! Form::number('options_data[min]', $options['min'] ?? null, ['class' => 'form-control', 'step' => 'any']) !!}
        </div>
        <div class="form-group col-6 col-md-3">
            {!! Form::label('options_data[max]', 'Max') !!}
            {!! Form::number('options_data[max]', $options['max'] ?? null, ['class' => 'form-control', 'step' => 'any']) !!}
        </div>
        <div class="form-group col-6 col-md-3">
            {!! Form::label('options_data[step]', 'Step') !!}
            {!! Form::number('options_data[step]', $options['step'] ?? null, ['class' => 'form-control', 'step' => 'any']) !!}
        </div>
        <div class="form-group col-6 col-md-3">
            {!! Form::label('options_data[unit]', 'Unit') !!}
            {!! Form::text('options_data[unit]', $options['unit'] ?? null, ['class' => 'form-control', 'placeholder' => 'i.e. px']) !!}
        </div>
    </div>

    <p class="text-muted mb-2">
        Unit is appended to a bare numerical value (i.e. <code>px</code> gets appended like <code>3px</code>). Leave blank for values that lack units, like line-height.
    </p>
</div>

{{-- HEADINGS... --}}
<div class="a11y-bases">
    {!! Form::label('options_data[bases]', 'Heading Base Sizes (rem)', ['class' => 'mb-0 font-weight-bold']) !!}
    <p class="text-muted mb-1">
        The unscaled size of each heading level, in rem. Defaults match Bootstrap; override these <b>only</b> if your theme restyled its heading sizes. The user's slider multiplies whatever is set here, so the hierarchy is kept.
    </p>

    <div class="form-row">
        @foreach (['h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $h)
            <div class="form-group col">
                {!! Form::label('options_data[bases][' . $h . ']', strtoupper($h), ['class' => 'font-weight-bold mb-0']) !!}
                {!! Form::number('options_data[bases][' . $h . ']', $options['bases'][$h] ?? null, ['class' => 'form-control', 'step' => 'any', 'data-level' => $h]) !!}
            </div>
        @endforeach
    </div>
</div>

{{-- CHOICE / SELECT DROPDOWN --}}
<div class="a11y-options" data-types="select">
    <div class="row no-gutters justify-content-between align-items-center">
        <div class="col-auto">
            {!! Form::label('choices_value', 'Choices', ['class' => 'mb-0 font-weight-bold']) !!}
        </div>
        <div class="col text-right">
            <a href="#" class="btn btn-outline-primary btn-sm a11y-add-row" data-field="choices">
                <i class="fas fa-plus" aria-hidden="true"></i> Add Choice
            </a>
        </div>
    </div>
    <p class="text-muted mb-1">
        Each choice has a <b>value</b> (the CSS value applied, i.e. a font family) and a <b>label</b> (what the user sees).<br>
        For example: value of <code>'Open Sans', sans-serif</code> is the what will be applied in CSS, and the user will see the label 'Open Sans'.
    </p>

    <div id="choicesList">
        @foreach ($choices as $choice)
            <div class="form-row a11y-choice-row mb-2">
                <div class="col-md">
                    {!! Form::text('choices_value[]', is_array($choice) ? ($choice['value'] ?? '') : $choice, ['class' => 'form-control', 'placeholder' => 'Value']) !!}
                </div>
                <div class="col-md">
                    {!! Form::text('choices_label[]', is_array($choice) ? ($choice['label'] ?? '') : '', ['class' => 'form-control', 'placeholder' => 'Label']) !!}
                </div>
                <div class="col-md-auto text-right">
                    <a href="#" class="btn btn-danger a11y-remove-row" aria-label="Remove"><i class="fas fa-times" aria-hidden="true"></i></a>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- COLOUR PRESETS --}}
<div class="a11y-options" data-types="color">
    <div class="row no-gutters justify-content-between align-items-center">
        <div class="col-auto">
            {!! Form::label('presets_value', 'Preset Color Options', ['class' => 'mb-0 font-weight-bold']) !!}
        </div>
        <div class="col text-right">
            <a href="#" class="btn btn-outline-primary btn-sm a11y-add-row" data-field="presets">
                <i class="fas fa-plus" aria-hidden="true"></i> Add Swatch
            </a>
        </div>
    </div>
    <p class="text-muted mb-1">
        Used when a colour setting is <b>constrained</b> (users pick a specific colour on a given list).<br>
        When not set to contrained, the setting is free-pick (users get a colour picker instead). Value is a colour; label is optional.
    </p>

    <div id="presetsList">
        @foreach ($presets as $preset)
            <div class="a11y-choice-row row mb-2">
                <div class="col-md">
                    <div class="input-group cp">
                        {!! Form::text('presets_value[]', is_array($preset) ? ($preset['value'] ?? '') : $preset, ['class' => 'form-control', 'placeholder' => '#000000']) !!}
                        <span class="input-group-append">
                            <span class="input-group-text colorpicker-input-addon"><i></i></span>
                        </span>
                    </div>
                </div>
                <div class="col-md">
                    {!! Form::text('presets_label[]', is_array($preset) ? ($preset['label'] ?? '') : '', ['class' => 'form-control', 'placeholder' => 'Label (optional)']) !!}
                </div>

                <div class="col-md-auto pl-1 text-right">
                    <a href="#" class="btn btn-danger a11y-remove-row" aria-label="Remove">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ON / OFF TOGGLE --}}
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
    <p class="text-muted mb-1">
        Simple on/off toggleable property. Leave blank to fall back to this target's config default.
    </p>
</div>

{{-- the clones...disabled to prevent that form submission issue of hidden form inputs passing thru --}}
<div class="a11y-choice-row a11y-clone form-row mb-2 hide" id="choicesClone">
    <div class="col-md">
        {!! Form::text('choices_value[]', null, ['class' => 'form-control', 'placeholder' => 'Value', 'disabled']) !!}
    </div>
    <div class="col-md">
        {!! Form::text('choices_label[]', null, ['class' => 'form-control', 'placeholder' => 'Label', 'disabled']) !!}
    </div>
    <div class="col-md-auto text-right">
        <a href="#" class="btn btn-danger a11y-remove-row" aria-label="Remove">
            <i class="fas fa-times" aria-hidden="true"></i>
        </a>
    </div>
</div>
<div class="a11y-choice-row a11y-clone row mb-2 hide" id="presetsClone">
    <div class="col-md">
        <div class="input-group cp">
            {!! Form::text('presets_value[]', null, ['class' => 'form-control', 'placeholder' => '#000000', 'disabled']) !!}
            <span class="input-group-append">
                <span class="input-group-text colorpicker-input-addon"><i></i></span>
            </span>
        </div>
    </div>
    <div class="col-md">
        {!! Form::text('presets_label[]', null, ['class' => 'form-control', 'placeholder' => 'Label (optional)', 'disabled']) !!}
    </div>
    <div class="col-md-auto pl-1 text-right">
        <a href="#" class="btn btn-danger a11y-remove-row" aria-label="Remove">
            <i class="fas fa-times" aria-hidden="true"></i>
        </a>
    </div>
</div>