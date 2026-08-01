@php
    $key = $setting->setting_key;
    $entry = $entry ?? [];
    $hasOverride = count($entry) > 0;
    $enabled = $entry['is_enabled'] ?? true;
    $type = $setting->input_type;

    $global = $setting->options_data ?? [];
    $stored = isset($entry['options_data']) && is_array($entry['options_data']) ? $entry['options_data'] : [];
    $choices = isset($stored['choices']) && is_array($stored['choices']) ? $stored['choices'] : [];
    $presets = isset($stored['presets']) && is_array($stored['presets']) ? $stored['presets'] : [];

    $prefix = 'overrides[' . $key . ']';
    $opt = $prefix . '[options_data]';

    // choices the user could actually pick under this theme, for the default dropdown
    $effectiveChoices = count($choices) ? $choices : (isset($global['choices']) && is_array($global['choices']) ? $global['choices'] : []);
    $defaultSelect = [];
    foreach ($effectiveChoices as $c) {
        $cVal = is_array($c) ? $c['value'] ?? '' : $c;
        $cLbl = is_array($c) ? $c['label'] ?? '' : '';
        if ($cVal != '') {
            $defaultSelect[$cVal] = $cLbl != '' ? $cLbl : $cVal;
        }
    }
@endphp

<div class="a11y-theme-row ubt-top mt-1 pt-2" data-key="{{ $key }}">
    <div class="row no-gutters align-items-center">
        <div class="col">
            <b>{{ $setting->name }}</b>
            <span class="badge badge-secondary ml-1">{{ ucfirst($type) }}</span>
            <code class="small ml-1">{{ $key }}</code>
        </div>
        <div class="col-auto">
            <a href="#" class="btn btn-outline-secondary btn-sm a11y-theme-toggle">
                <i class="fas {{ $hasOverride ? 'fa-chevron-up' : 'fa-chevron-down' }} mr-1" aria-hidden="true"></i><span class="a11y-theme-toggle-text">{{ $hasOverride ? 'Hide' : 'Customize' }}</span>
            </a>
        </div>
    </div>

    <div class="a11y-theme-body pl-2 pt-2 pb-1 {{ $hasOverride ? '' : 'hide' }}">
        <div class="form-row">
            <div class="form-group col-md-4">
                {!! Form::label($prefix . '[is_enabled]', 'On This Theme', ['class' => 'mb-1 d-block']) !!}
                {!! Form::checkbox($prefix . '[is_enabled]', 1, $enabled, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => 'Offered', 'data-off' => 'Hidden', 'data-onstyle' => 'success', 'data-offstyle' => 'secondary']) !!}
            </div>

            <div class="form-group col-md-8">
                {!! Form::label($prefix . '[default_value]', 'Theme Default') !!}
                @switch ($type)
                    @case('color')
                        <div class="input-group cp">
                            {!! Form::text($prefix . '[default_value]', $entry['default_value'] ?? null, ['class' => 'form-control', 'placeholder' => $setting->default_value ?? 'global default']) !!}
                            <span class="input-group-append">
                                <span class="input-group-text colorpicker-input-addon"><i></i></span>
                            </span>
                        </div>
                    @break

                    @case('select')
                        {!! Form::select($prefix . '[default_value]', $defaultSelect, $entry['default_value'] ?? null, ['class' => 'form-control', 'placeholder' => 'Global default']) !!}
                    @break

                    @case('toggle')
                        {!! Form::select($prefix . '[default_value]', ['1' => 'On', '0' => 'Off'], $entry['default_value'] ?? null, ['class' => 'form-control', 'placeholder' => 'Global default']) !!}
                    @break

                    @case('range')
                    @case('number')
                        {!! Form::number($prefix . '[default_value]', $entry['default_value'] ?? null, ['class' => 'form-control', 'step' => 'any', 'placeholder' => $setting->default_value ?? 'global default']) !!}
                    @break

                    @default
                        {!! Form::text($prefix . '[default_value]', $entry['default_value'] ?? null, ['class' => 'form-control', 'placeholder' => $setting->default_value ?? 'global default']) !!}
                    @break
                @endswitch
            </div>
        </div>

        @switch ($type)
            @case('range')
            @case('number')
                <div class="form-row">
                    <div class="form-group col-6 col-md-3">
                        {!! Form::label($opt . '[min]', 'Min') !!}
                        {!! Form::number($opt . '[min]', $stored['min'] ?? null, ['class' => 'form-control', 'step' => 'any', 'placeholder' => $global['min'] ?? 'global']) !!}
                    </div>
                    <div class="form-group col-6 col-md-3">
                        {!! Form::label($opt . '[max]', 'Max') !!}
                        {!! Form::number($opt . '[max]', $stored['max'] ?? null, ['class' => 'form-control', 'step' => 'any', 'placeholder' => $global['max'] ?? 'global']) !!}
                    </div>
                    <div class="form-group col-6 col-md-3">
                        {!! Form::label($opt . '[step]', 'Step') !!}
                        {!! Form::number($opt . '[step]', $stored['step'] ?? null, ['class' => 'form-control', 'step' => 'any', 'placeholder' => $global['step'] ?? 'global']) !!}
                    </div>
                    <div class="form-group col-6 col-md-3">
                        {!! Form::label($opt . '[unit]', 'Unit') !!}
                        {!! Form::text($opt . '[unit]', $stored['unit'] ?? null, ['class' => 'form-control', 'placeholder' => $global['unit'] ?? 'i.e. px']) !!}
                    </div>
                </div>
                <p class="text-muted small mb-1">
                    Leave a field blank to inherit the global option for this theme.
                </p>
            @break

            @case('toggle')
                <div class="form-row">
                    <div class="form-group col-md-6">
                        {!! Form::label($opt . '[on_value]', 'On Value') !!}
                        {!! Form::text($opt . '[on_value]', $stored['on_value'] ?? null, ['class' => 'form-control', 'placeholder' => $global['on_value'] ?? 'i.e. contrast(1.25)']) !!}
                    </div>
                    <div class="form-group col-md-6">
                        {!! Form::label($opt . '[off_value]', 'Off Value') !!}
                        {!! Form::text($opt . '[off_value]', $stored['off_value'] ?? null, ['class' => 'form-control', 'placeholder' => $global['off_value'] ?? 'leave blank for none']) !!}
                    </div>
                </div>
                <p class="text-muted small mb-1">
                    Leave blank to inherit the global on/off values for this theme.
                </p>
            @break

            @case('select')
                <div class="row no-gutters justify-content-between align-items-center">
                    <div class="col-auto">
                        {!! Form::label($opt . '[choices]', 'Choices', ['class' => 'mb-0 font-weight-bold']) !!}
                    </div>
                    <div class="col text-right">
                        <a href="#" class="btn btn-outline-primary btn-sm a11y-add-row" data-field="choices" data-key="{{ $key }}">
                            <i class="fas fa-plus" aria-hidden="true"></i> Add Choice
                        </a>
                    </div>
                </div>
                <p class="text-muted small mb-1">
                    Add choices to replace the global list on this theme. Leave empty to inherit the global choices.
                </p>
                <div id="choicesList-{{ $key }}">
                    @foreach ($choices as $choice)
                        <div class="form-row a11y-choice-row mb-2">
                            <div class="col-md">
                                {!! Form::text($opt . '[choices_value][]', is_array($choice) ? $choice['value'] ?? '' : $choice, ['class' => 'form-control', 'placeholder' => 'Value']) !!}
                            </div>
                            <div class="col-md">
                                {!! Form::text($opt . '[choices_label][]', is_array($choice) ? $choice['label'] ?? '' : '', ['class' => 'form-control', 'placeholder' => 'Label']) !!}
                            </div>
                            <div class="col-md-auto text-right">
                                <a href="#" class="btn btn-danger a11y-remove-row" aria-label="Remove"><i class="fas fa-times" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="form-row a11y-choice-row a11y-clone hide mb-2" id="choicesClone-{{ $key }}">
                    <div class="col-md">
                        {!! Form::text($opt . '[choices_value][]', null, ['class' => 'form-control', 'placeholder' => 'Value', 'disabled']) !!}
                    </div>
                    <div class="col-md">
                        {!! Form::text($opt . '[choices_label][]', null, ['class' => 'form-control', 'placeholder' => 'Label', 'disabled']) !!}
                    </div>

                    <div class="col-md-auto text-right">
                        <a href="#" class="btn btn-danger a11y-remove-row" aria-label="Remove">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            @break

            @case('color')
                <div class="row no-gutters justify-content-between align-items-center">
                    <div class="col-auto">
                        {!! Form::label($opt . '[presets]', 'Preset Swatches', ['class' => 'mb-0 font-weight-bold']) !!}
                    </div>
                    <div class="col text-right">
                        <a href="#" class="btn btn-outline-primary btn-sm a11y-add-row" data-field="presets" data-key="{{ $key }}">
                            <i class="fas fa-plus" aria-hidden="true"></i> Add Swatch
                        </a>
                    </div>
                </div>
                <p class="text-muted small mb-1">Swatches for constrained colour settings. Leave empty to inherit the global swatches on this theme.</p>
                <div id="presetsList-{{ $key }}">
                    @foreach ($presets as $preset)
                        <div class="a11y-choice-row row mb-2">
                            <div class="col-md">
                                <div class="input-group cp">
                                    {!! Form::text($opt . '[presets_value][]', is_array($preset) ? $preset['value'] ?? '' : $preset, ['class' => 'form-control', 'placeholder' => '#000000']) !!}
                                    <span class="input-group-append">
                                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md">
                                {!! Form::text($opt . '[presets_label][]', is_array($preset) ? $preset['label'] ?? '' : '', ['class' => 'form-control', 'placeholder' => 'Label (optional)']) !!}
                            </div>
                            <div class="col-md-auto pl-1 text-right">
                                <a href="#" class="btn btn-danger a11y-remove-row" aria-label="Remove"><i class="fas fa-times" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="a11y-choice-row a11y-clone row mb-2 hide" id="presetsClone-{{ $key }}">
                    <div class="col-md">
                        <div class="input-group cp">
                            {!! Form::text($opt . '[presets_value][]', null, ['class' => 'form-control', 'placeholder' => '#000000', 'disabled']) !!}
                            <span class="input-group-append">
                                <span class="input-group-text colorpicker-input-addon"><i></i></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md">
                        {!! Form::text($opt . '[presets_label][]', null, ['class' => 'form-control', 'placeholder' => 'Label (optional)', 'disabled']) !!}
                    </div>
                    <div class="col-md-auto pl-1 text-right">
                        <a href="#" class="btn btn-danger a11y-remove-row" aria-label="Remove"><i class="fas fa-times" aria-hidden="true"></i></a>
                    </div>
                </div>
            @break

        @endswitch
    </div>
</div>
