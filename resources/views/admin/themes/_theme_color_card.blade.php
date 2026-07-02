<div class="col-md-6 p-2">
    <div class="card">
        <div class="card-header font-weight-bold p-2">
            {{ $entry['label'] }}
            <span class="small">(<code>${{ $key }}</code>)</span>
        </div>
        <div class="card-body py-3">
            <div class="row align-items-start">
                <div class="col-12 col-md">
                    <div class="form-group mb-0">
                        {!! Form::label('color_data[' . $key . ']', 'Color', ['class' => 'font-weight-bold mb-0']) !!}
                        @include('admin.themes._color_field', [
                            'name' => 'color_data[' . $key . ']',
                            'value' => $value,
                            'placeholder' => $entry['default'],
                        ])
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="form-group mb-0">
                        {!! Form::label('theme_color_data[' . $key . '][lighten]', 'Direction', ['class' => 'font-weight-bold mb-1']) !!}
                        <div>
                            {!! Form::checkbox(
                                'theme_color_data[' . $key . '][lighten]',
                                1,
                                $lighten,
                                [
                                    'class'         => 'form-check-input',
                                    'data-toggle'   => 'toggle',
                                    'data-on'       => 'Lighten',
                                    'data-off'      => 'Darken',
                                    'data-onstyle'  => 'light border',
                                    'data-offstyle' => 'dark',
                                    'data-size'     => 'sm',
                                ]
                            ) !!}
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4">
                    <div class="form-group mb-0">
                        {!! Form::label('theme_color_data[' . $key . '][step]', 'Step %', ['class' => 'font-weight-bold mb-0']) !!}
                        {!! add_help('Percentage shift per increment. Controls hover/active intensity and the spacing of the generated <code>--' . $key . '-100</code>..<code>900</code> CSS variables.') !!}
                        {!! Form::number(
                            'theme_color_data[' . $key . '][step]',
                            $step,
                            ['class' => 'form-control', 'min' => 1, 'max' => 25, 'placeholder' => '-']
                        ) !!}
                        <div class="small mt-1">
                            <a href="#" class="set-step-default">Default ({{ $stepDefault }}%)</a>
                            <span class="text-muted">·</span>
                            <a href="#" class="clear-step">Clear</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
