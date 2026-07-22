@php
    $a11yType = $setting->input_type;
    $a11yChoices = isset($options['choices']) && is_array($options['choices']) ? $options['choices'] : [];
    $a11yPresets = isset($options['presets']) && is_array($options['presets']) ? $options['presets'] : [];
    $a11yMin = $options['min'] ?? 0;
    $a11yMax = $options['max'] ?? 100;
    $a11yStep = $options['step'] ?? 1;
    $a11yUnit = $options['unit'] ?? '';
    $a11yName = $setting->setting_key;
    $a11ySelectOptions = [];
    foreach ($a11yChoices as $choice) {
        $cv = is_array($choice) ? ($choice['value'] ?? '') : $choice;
        $a11ySelectOptions[$cv] = is_array($choice) ? ($choice['label'] ?? $cv) : $choice;
    }
@endphp
<div class="form-group a11y-control" data-a11y-setting="{{ $setting->setting_key }}" data-a11y-id="{{ $setting->id }}" data-a11y-type="{{ $a11yType }}">
    <div class="d-flex justify-content-between align-items-center">
        {!! Form::label($a11yName, $setting->name, ['class' => 'mb-1 font-weight-bold']) !!}
        <a href="#" class="small text-muted a11y-reset-one" data-a11y-id="{{ $setting->id }}">Reset</a>
    </div>
    @if ($setting->description)
        <p class="text-muted small mb-1">
            {{ $setting->description }}
        </p>
    @endif

    @switch ($a11yType)
        @case('range')
            <div class="d-flex align-items-center">
                {!! Form::range($a11yName, $value, ['class' => 'custom-range a11y-input mr-3', 'min' => $a11yMin, 'max' => $a11yMax, 'step' => $a11yStep, 'style' => 'flex: 1;']) !!}
                <span class="a11y-range-value small text-muted" style="min-width: 4em; text-align: right;">
                    {{ $value }}{{ $a11yUnit }}
                </span>
            </div>
            @if ($default != '')
                <small class="text-muted">Default: {{ $default }}{{ $a11yUnit }}</small>
            @endif
        @break

        @case('number')
            {!! Form::number($a11yName, $value, ['class' => 'form-control a11y-input', 'min' => $a11yMin, 'max' => $a11yMax, 'step' => $a11yStep, 'placeholder' => $default]) !!}
        @break

        @case('select')
            {!! Form::select($a11yName, $a11ySelectOptions, $value, ['class' => 'form-control a11y-input', 'placeholder' => $a11ySelectOptions[$default] ?? 'Default']) !!}
        @break

        @case('toggle')
            <div>
                {!! Form::checkbox($a11yName, 1, $value == '1' || $value == 'on', ['class' => 'a11y-input', 'data-toggle' => 'toggle']) !!}
            </div>
        @break

        @case('color')
            @if ($setting->is_constrained)
                {!! Form::hidden($a11yName, $value, ['class' => 'a11y-input']) !!}
                <div class="a11y-swatches">
                    @foreach ($a11yPresets as $preset)
                        @php
                            $pv = is_array($preset) ? ($preset['value'] ?? '') : $preset;
                            $pl = is_array($preset) ? ($preset['label'] ?? $pv) : $preset;
                        @endphp
                        <a href="#" class="a11y-swatch d-inline-block mr-1 mb-1 {{ $value == $pv ? 'active' : '' }}" data-value="{{ $pv }}" data-toggle="tooltip" title="{{ $pl }}" style="width: 28px; height: 28px; border-radius: 4px; border: 2px solid rgba(0,0,0,.15); background-color: {{ $pv }}; vertical-align: middle;"></a>
                    @endforeach
                </div>
            @else
                {!! Form::text($a11yName, $value, ['class' => 'form-control cp a11y-input', 'placeholder' => $default]) !!}
            @endif
        @break
    @endswitch
</div>
