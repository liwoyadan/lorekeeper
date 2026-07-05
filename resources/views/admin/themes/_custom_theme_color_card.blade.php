<div class="card custom-theme-color-row my-2">
    <div class="card-body p-2">
        <div class="row no-gutters align-items-start">
            <div class="col-12 p-1 col-md">
                <div class="form-group mb-0">
                    {!! Form::label('custom_scss_data[theme_colors][name][]', 'Name', ['class' => 'font-weight-bold mb-0']) !!}
                    {!! Form::text('custom_scss_data[theme_colors][name][]', $name, ['class' => 'form-control', 'placeholder' => 'i.e. brand']) !!}
                </div>
            </div>

            <div class="col-12 p-1 col-md-4">
                <div class="form-group mb-0">
                    {!! Form::label('custom_scss_data[theme_colors][value][]', 'Color', ['class' => 'font-weight-bold mb-0']) !!}
                    @include('admin.themes._color_field', [
                        'name' => 'custom_scss_data[theme_colors][value][]',
                        'value' => $value,
                        'placeholder' => '#000000',
                    ])
                </div>
            </div>

            <div class="col-6 p-1 col-md-2">
                <div class="form-group mb-0">
                    {!! Form::label('custom_scss_data[theme_colors][lighten][]', 'Direction', ['class' => 'font-weight-bold mb-0']) !!}
                    {!! Form::select('custom_scss_data[theme_colors][lighten][]', [0 => 'Darken', 1 => 'Lighten'], $lighten, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="col-6 p-1 col-md-2">
                <div class="form-group mb-0">
                    {!! Form::label('custom_scss_data[theme_colors][step][]', 'Step %', ['class' => 'font-weight-bold mb-0']) !!}
                    {!! Form::number('custom_scss_data[theme_colors][step][]', $step, ['class' => 'form-control', 'min' => 1, 'max' => 25, 'placeholder' => '-']) !!}
                    <div class="small mt-1">
                        <a href="#" class="set-step-default">Default ({{ $stepDefault }}%)</a>
                        <span class="text-muted">·</span>
                        <a href="#" class="clear-step">Clear</a>
                    </div>
                </div>
            </div>

            <div class="col-auto p-1 align-self-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-custom-theme-color">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>
