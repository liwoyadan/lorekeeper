@php
    if ($name && !in_array($name, array_merge(...array_values($variableOptions)))) {
        $variableOptions[$name] = $name;
    }
@endphp
<div class="form-row align-items-start custom-variable-row mb-2">
    <div class="col-5">
        {!! Form::select('custom_scss_data[custom_variables][name][]', $variableOptions, $name, ['class' => 'form-control custom-variable-name', 'placeholder' => 'Select or type a variable']) !!}
    </div>
    <div class="col">
        {!! Form::text('custom_scss_data[custom_variables][value][]', $value, ['class' => 'form-control custom-variable-value', 'placeholder' => 'value (i.e. #fff or 0.5rem)']) !!}
    </div>
    <div class="col-auto">
        <button type="button" class="btn btn-outline-danger remove-custom-variable"><i class="fas fa-trash"></i></button>
    </div>
</div>
