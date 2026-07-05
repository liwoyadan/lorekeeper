@extends('admin.layout')

@section('admin-title')
    {{ $bootstrap->id ? 'Edit Bootstrap Theme: ' . $bootstrap->name : 'Create Bootstrap Theme' }}
@endsection

@push('head')
    <style id="tooltipPreviewStyle"></style>
@endpush

@section('admin-content')
    @php
        $colorData = $bootstrap->color_data ?? [];
        $themeColorData = $bootstrap->theme_color_data ?? [];
        $styleData = $bootstrap->style_data ?? [];
        $customThemeColors = $bootstrap->custom_scss_data['theme_colors'] ?? [];

        $variableOptions = [];
        foreach (config('lorekeeper.themes.common_variables') as $group => $vars) {
            $variableOptions[$group] = array_combine($vars, $vars);
        }
    @endphp

    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Themes' => 'admin/themes',
        'Bootstrap Themes' => 'admin/bootstrap-themes',
        ($bootstrap->id ? 'Edit' : 'Create') . ' Bootstrap Theme' => $bootstrap->id ? 'admin/bootstrap-themes/edit/' . $bootstrap->id : 'admin/bootstrap-themes/create',
    ]) !!}

    <h1>
        {{ $bootstrap->id ? 'Edit ' . $bootstrap->name : 'Create Bootstrap Theme' }}

        @if ($bootstrap->id)
            <a href="#" class="btn btn-danger float-right delete-bootstrap-theme-button">Delete Bootstrap Theme</a>
        @endif
    </h1>

    {!! Form::open(['url' => $bootstrap->id ? 'admin/bootstrap-themes/edit/' . $bootstrap->id : 'admin/bootstrap-themes/create']) !!}

    {{-- BASIC INFO --}}
    <h5 class="mb-1">Basic Information</h5>
    <div class="form-group">
        {!! Form::label('name', 'Name') !!}
        {!! Form::text('name', $bootstrap->name, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group text-right">
        <div class="form-check">
            {!! Form::checkbox('is_default', 1, $bootstrap->is_default, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_default', 'Site Default', ['class' => 'form-check-label ml-3']) !!}
            {!! add_help(
                'When set, this Bootstrap is used as the site\'s overall default Bootstrap theme, including as a fallback for any themes that do not have a Bootstrap theme assigned, and will display instead of Lorekeeper\'s default. Only one Bootstrap theme can be the default at any given time; saving this while On will unset any other(s) set as default!',
            ) !!}
        </div>
    </div>

    <hr />

    {{-- OPTIONS --}}
    <h4 class="mb-1">Toggleable Options</h4>
    <p>
        Bootstrap theme-wide switches that turn whole families of styling on or off across every component (i.e. make all components have rounded corners).<br>
        Each maps to a Bootstrap <code>$enable-*</code> flag.
    </p>
    <div class="row">
        @foreach ($toggles as $key => $entry)
            <div class="col-12 col-md-6 mb-3">
                <div class="d-flex align-items-center">
                    {!! Form::checkbox('style_data[' . $key . ']', 1, $styleData[$key] ?? $entry['default'], ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-size' => 'sm', 'data-onstyle' => 'success', 'data-offstyle' => 'secondary']) !!}
                    <span class="ml-3">
                        <b>{{ $entry['label'] }}</b> <span class="small">(<code>${{ $key }}</code>)</span>
                    </span>
                </div>
                <div class="text-muted small mt-1">
                    {{ $entry['help'] }}
                </div>
            </div>
        @endforeach
    </div>

    <hr />

    {{-- BASE COLORS --}}
    <h4 class="mb-1">Base Colors</h4>
    <p>
        Overrides Bootstrap's named color variables (i.e. <code>$blue</code>, <code>$red</code>). Leave blank to use the default shown.
    </p>
    <div class="row">
        @foreach ($baseColors as $key => $entry)
            <div class="col-6 col-md-4 col-lg-2">
                <div class="form-group">
                    {!! Form::label('color_data[' . $key . ']', $entry['label']) !!}
                    <span class="small">(<code>${{ $key }}</code>)</span>
                    @include('admin.themes._color_field', [
                        'name' => 'color_data[' . $key . ']',
                        'value' => $colorData[$key] ?? null,
                        'placeholder' => $entry['default'],
                    ])
                </div>
            </div>
        @endforeach
    </div>

    <hr class="w-75 mx-auto" />

    {{-- GRAY SCALE --}}
    <h5 class="mb-1">Gray Scale</h5>
    <p>
        Overrides Bootstrap's grayscale variables. Leave blank to use the default shown.
    </p>
    <div class="row">
        @foreach ($grays as $key => $entry)
            <div class="col-6 col-md-4 col-lg-2">
                <div class="form-group">
                    {!! Form::label('color_data[' . $key . ']', $entry['label']) !!}
                    <span class="small">(<code>${{ $key }}</code>)</span>
                    @include('admin.themes._color_field', [
                        'name' => 'color_data[' . $key . ']',
                        'value' => $colorData[$key] ?? null,
                        'placeholder' => $entry['default'],
                    ])
                </div>
            </div>
        @endforeach
    </div>

    <hr />

    {{-- THEME COLORS --}}
    <h4 class="mb-1">Theme Colors</h4>
    <p>
        Sets Bootstrap's theme color variables (i.e. <code>$primary</code>, <code>$danger</code>). Leave color blank to use the default shown.<br>
        <b>Direction</b> picks whether hover/active states lighten or darken the color.<br>
    </p>
    <p>
        <b>Step</b> sets the percentage shift (darker/lighter) per increment (also used to generate a set of CSS variables from <code>--{theme}-100</code> through <code>--{theme}-900</code>, with the picked color set in the middle as <code>-500</code>
        ).<br>
        For example, if you set the step to <b>5</b>, then <code>$primary-600</code> will be 5% darker and <code>$primary-700</code> is 10% darker than your chosen color. Likewise, <code>$primary-400</code> is 5% lighter and so on.<br>
        Step maxes out at <b>25%</b>, since Bootstrap won't compute values past #fff white or #000 black.
    </p>
    <div class="text-right">
        <button type="button" class="btn btn-sm btn-outline-primary set-all-steps" data-target="#themeColorCards">
            Set all to {{ $stepDefault }}%
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary clear-all-steps" data-target="#themeColorCards">
            Clear all steps
        </button>
    </div>
    <div class="row no-gutters" id="themeColorCards">
        @foreach ($themeColors as $key => $entry)
            @include('admin.themes._theme_color_card', [
                'key' => $key,
                'entry' => $entry,
                'value' => $colorData[$key] ?? null,
                'lighten' => $themeColorData[$key]['lighten'] ?? 0,
                'step' => $themeColorData[$key]['step'] ?? null,
            ])
        @endforeach
    </div>

    <hr class="w-75 mx-auto" />

    {{-- CUSTOM THEME COLORS --}}
    <h5 class="mb-1">Custom Theme Colors</h5>
    <p>
        Add your own named entries to Bootstrap's <code>$theme-colors</code> map (by default this is: primary, secondary, success, danger, warning, info, light, and dark). Each one generates a full family of classes for themed components, so a color
        named <code>brand</code> yields <code>.btn-brand</code>, <code>.btn-outline-brand</code>,
        <code>.bg-brand</code>, <code>.text-brand</code>, <code>.border-brand</code>, <code>.alert-brand</code>, and so on.<br>
        Names are <b>lowercase</b> and limited to <b>letters</b>, <b>numbers</b>, and <b>hyphens</b>; do not include the dollar sign. Color must be a literal value (hexcode, <code>rgb()</code>, or a named color); SCSS variables like <code>$blue</code>
        are not supported here.<br>
        <b>Direction</b> and <b>Step</b> work just like the Theme Colors above: set a step to generate hover/active states plus the <code>--{name}-100</code>..<code>900</code> scale. Leave step blank for a plain color with stock Bootstrap hover.
    </p>
    <div class="text-right mb-1">
        <button type="button" class="btn btn-outline-primary btn-sm" id="addCustomThemeColor">
            <i class="fas fa-plus"></i> Add Color
        </button>
    </div>
    <div id="customThemeColorRows">
        @foreach ($customThemeColors as $name => $entry)
            @include('admin.themes._custom_theme_color_card', [
                'name' => $name,
                'value' => $entry['value'] ?? null,
                'step' => $entry['step'] ?? null,
                'lighten' => $entry['lighten'] ?? 0,
            ])
        @endforeach
    </div>
    <div class="text-right mt-1">
        <button type="button" class="btn btn-sm btn-outline-primary set-all-steps" data-target="#customThemeColorRows">
            Set all to {{ $stepDefault }}%
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary clear-all-steps" data-target="#customThemeColorRows">
            Clear all steps
        </button>
    </div>

    <hr />

    {{-- TYPOGRAPHY --}}
    <h4 class="mb-1">Typography & Borders</h4>
    <p>
        Sets Bootstrap's <code>$body-color</code> and <code>$headings-color</code> variables, as well as generating CSS variables like <code>--body-color</code> and <code>--headings-color</code>.<br>
        Leave blank to use default set in config.
    </p>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('style_data[body-color]', $typography['body-color']['label']) !!}
                <span class="small">
                    (<code>$body-color</code>)
                </span>
                @include('admin.themes._color_field', [
                    'name' => 'style_data[body-color]',
                    'value' => $styleData['body-color'] ?? null,
                    'placeholder' => $typography['body-color']['default'],
                ])
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('style_data[headings-color]', $typography['headings-color']['label']) !!}
                <span class="small">
                    (<code>$headings-color</code>)
                </span>
                @include('admin.themes._color_field', [
                    'name' => 'style_data[headings-color]',
                    'value' => $styleData['headings-color'] ?? null,
                    'placeholder' => $typography['headings-color']['default'],
                ])
            </div>
        </div>
    </div>

    <hr class="w-75 mx-auto" />

    {{-- BORDERS & RADIUS --}}
    <p>
        Sets Bootstrap's <code>$border-radius</code>, <code>$border-width</code>, and <code>$border-color</code> variables, plus a custom <code>$border-style</code>.<br>
        All four also generate CSS variables <code>--border-*</code>. Leave blank to use the default shown.
    </p>
    <div class="row">
        <div class="col-6 col-md-3">
            <div class="form-group">
                {!! Form::label('style_data[border-radius]', $styles['border-radius']['label']) !!}
                <span class="small">
                    (<code>$border-radius</code>)
                </span>
                {!! Form::text('style_data[border-radius]', $styleData['border-radius'] ?? null, ['class' => 'form-control', 'placeholder' => $styles['border-radius']['default']]) !!}
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="form-group">
                {!! Form::label('style_data[border-width]', $styles['border-width']['label']) !!}
                <span class="small">
                    (<code>$border-width</code>)
                </span>
                <div class="input-group">
                    {!! Form::number('style_data[border-width]', $styleData['border-width'] ?? null, ['class' => 'form-control', 'min' => 0, 'step' => 'any', 'placeholder' => $styles['border-width']['default']]) !!}
                    <span class="input-group-append">
                        <span class="input-group-text">px</span>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="form-group">
                {!! Form::label('style_data[border-color]', $styles['border-color']['label']) !!}
                <span class="small">
                    (<code>$border-color</code>)
                </span>
                @include('admin.themes._color_field', [
                    'name' => 'style_data[border-color]',
                    'value' => $styleData['border-color'] ?? null,
                    'placeholder' => $styles['border-color']['default'],
                ])
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="form-group">
                {!! Form::label('style_data[border-style]', $styles['border-style']['label']) !!}
                <span class="small">
                    (<code>$border-style</code>)
                </span>
                {!! Form::select('style_data[border-style]', array_combine($borderStyles, array_map('ucfirst', $borderStyles)), $styleData['border-style'] ?? null, ['class' => 'form-control', 'placeholder' => $styles['border-style']['default']]) !!}
            </div>
        </div>
    </div>

    <hr />

    {{-- COMPONENT VARIABLES --}}
    <h4 class="mb-1">Component Variables</h4>
    <p>
        A handful of commonly tweaked Bootstrap variables.<br>
        Leave any field blank to keep config's (or Bootstrap's, if config is not edited) default, which may use values from your other choices/variables.<br>
        (i.e. <code>$text-muted</code> by default uses <code>$gray-600</code>, and the tooltip and thumbnail radius uses <code>$border-radius</code>)
    </p>

    @php
        $variableSections = [['heading' => null, 'fields' => $extras], ['heading' => 'Tooltips', 'fields' => $tooltips], ['heading' => 'Thumbnails', 'fields' => $thumbnails]];
    @endphp
    @foreach ($variableSections as $section)
        @if ($section['heading'])
            @if (!$loop->first)
                <hr>
            @endif
            <h5 class="my-1">
                {{ $section['heading'] }}
            </h5>
        @endif

        <div class="row no-gutters align-items-center">
            <div class="{{ $section['heading'] ? 'col-md-6 pr-md-1' : 'col-12' }}">
                <div class="row no-gutters">
                    @foreach ($section['fields'] as $key => $entry)
                        <div class="col-6 p-1">
                            <div class="form-group mb-0">
                                {!! Form::label('style_data[' . $key . ']', $entry['label']) !!}
                                <span class="small">
                                    (<code>${{ $key }}</code>)
                                </span>
                                @if ($entry['type'] == 'color')
                                    @include('admin.themes._color_field', [
                                        'name' => 'style_data[' . $key . ']',
                                        'value' => $styleData[$key] ?? null,
                                        'placeholder' => $entry['default'],
                                    ])
                                @elseif ($entry['type'] == 'width')
                                    <div class="input-group">
                                        {!! Form::number('style_data[' . $key . ']', $styleData[$key] ?? null, ['class' => 'form-control', 'min' => 0, 'step' => 'any', 'placeholder' => $entry['default']]) !!}
                                        <span class="input-group-append"><span class="input-group-text">px</span></span>
                                    </div>
                                @elseif ($entry['type'] == 'opacity')
                                    {!! Form::number('style_data[' . $key . ']', $styleData[$key] ?? null, ['class' => 'form-control', 'min' => 0, 'max' => 1, 'step' => '0.05', 'placeholder' => $entry['default']]) !!}
                                @else
                                    {!! Form::text('style_data[' . $key . ']', $styleData[$key] ?? null, ['class' => 'form-control', 'placeholder' => $entry['default']]) !!}
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if ($section['heading'] == 'Thumbnails')
                        <div class="col-12 p-1">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="refreshThumbnailPreview">
                                <i class="fas fa-sync"></i> Refresh Preview
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            @if ($section['heading'])
                <div class="col-md-6 pl-md-1 text-center">
                    @switch ($section['heading'])
                        @case('Thumbnails')
                            <div>
                                <img id="thumbnailPreview" class="img-thumbnail" src="{{ asset('images/myo-th.png') }}">
                            </div>
                        @break

                        @case('Tooltips')
                            <button type="button" class="btn btn-secondary" id="tooltipPreviewBtn">
                                Click Me
                            </button>
                        @break
                    @endswitch
                </div>
            @endif
        </div>
    @endforeach

    <hr />

    {{-- CUSTOM VARIABLES --}}
    <h4 class="mb-1">Custom Variables</h4>
    <p>
        Override common Bootstrap variables for individual components. You can either pick one from the list (grouped by component, i.e. card) or type any Bootstrap variable name to add your own.<br>
        Names are <b>lowercase</b> letters, <b>numbers</b>, and <b>hyphens</b>; do not include the dollar sign. Values must be literal (a hexcode, a size like 0.5rem, etc.); SCSS variables like <code>$primary</code> are <u>not</u> supported here. These
        are applied <i>before</i> Bootstrap compiles, so they override its defaults.
    </p>
    <div class="text-right mb-2">
        <button type="button" class="btn btn-outline-primary btn-sm" id="addCustomVariable">
            <i class="fas fa-plus"></i> Add Variable
        </button>
    </div>
    <div id="customVariableRows">
        @foreach ($bootstrap->custom_scss_data['custom_variables'] ?? [] as $name => $value)
            @include('admin.themes._custom_theme_row', ['name' => $name, 'value' => $value, 'variableOptions' => $variableOptions])
        @endforeach
    </div>

    <hr />

    {{-- CUSTOM SCSS --}}
    <h4 class="mb-1">Custom SCSS</h4>
    <p>
        Write raw SCSS here to be injected verbatim into compilation. 'Prepend' is added before, and 'append' is added after.<br>
        Editors collapsed by default to keep the page short.
    </p>
    <div class="accordion mb-2" id="customScssAccordion">
        <div class="card">
            <div class="card-header py-2 px-1 border-0" id="prependHeading">
                <button class="btn btn-link btn-block text-left collapsed h5 mb-0" type="button" data-toggle="collapse" data-target="#prependCollapse" aria-expanded="false" aria-controls="prependCollapse">
                    Prepend SCSS {!! add_help('Injected <b>before</b> Bootstrap variables. Use to define SCSS variable overrides and mixins.') !!}
                </button>
            </div>
            <div id="prependCollapse" class="collapse" aria-labelledby="prependHeading" data-parent="#customScssAccordion">
                <div class="card-body border-top">
                    {!! Form::textarea('custom_prepend', $bootstrap->custom_prepend, ['id' => 'customPrepend']) !!}
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header py-2 px-1 border-0" id="appendHeading">
                <button class="btn btn-link btn-block text-left collapsed h5 mb-0" type="button" data-toggle="collapse" data-target="#appendCollapse" aria-expanded="false" aria-controls="appendCollapse">
                    Append SCSS {!! add_help('Injected <b>after</b> Bootstrap. Use to add extra SCSS rules or overrides.') !!}
                </button>
            </div>
            <div id="appendCollapse" class="collapse" aria-labelledby="appendHeading" data-parent="#customScssAccordion">
                <div class="card-body border-top">
                    {!! Form::textarea('custom_append', $bootstrap->custom_append, ['id' => 'customAppend']) !!}
                </div>
            </div>
        </div>
    </div>

    <hr />

    <div class="text-right mt-2">
        <button type="submit" name="action" value="save" class="btn btn-outline-primary bootstrap-theme-submit">
            {{ $bootstrap->id ? 'Save Changes' : 'Create' }}
        </button>
        <button type="submit" name="action" value="save_compile" class="btn btn-primary ml-1 bootstrap-theme-submit">
            {{ $bootstrap->id ? 'Save & Compile' : 'Create & Compile' }}
        </button>
    </div>

    {!! Form::close() !!}

    <div id="customThemeColorRowData" class="hide">
        @include('admin.themes._custom_theme_color_card', ['name' => null, 'value' => null, 'step' => null, 'lighten' => 0])
    </div>

    <div id="customVariableRowData" class="hide">
        @include('admin.themes._custom_theme_row', ['name' => null, 'value' => null, 'variableOptions' => $variableOptions])
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            [{
                    textarea: 'customPrepend',
                    collapse: '#prependCollapse'
                },
                {
                    textarea: 'customAppend',
                    collapse: '#appendCollapse'
                },
            ].forEach(function(cfg) {
                var editor = CodeMirror.fromTextArea(document.getElementById(cfg.textarea), {
                    mode: 'text/x-scss',
                    theme: 'dracula',
                    lineNumbers: true,
                    lineWrapping: true,
                    indentWithTabs: false,
                    tabSize: 4,
                });

                $(cfg.collapse).on('shown.bs.collapse', function() {
                    editor.refresh();
                });
            });

            // The global .cp initializer also binds this hidden prototype; tear it down so each clone starts from clean markup.
            $('#customThemeColorRowData .cp').colorpicker('destroy');
            var $customThemeColorRow = $('#customThemeColorRowData').find('.custom-theme-color-row');

            $('#addCustomThemeColor').on('click', function(e) {
                e.preventDefault();
                var $clone = $customThemeColorRow.clone();
                $('#customThemeColorRows').append($clone);
                $clone.find('.cp').colorpicker({
                    autoInputFallback: false,
                    autoHexInputFallback: false,
                    format: 'auto',
                    useAlpha: true,
                    extensions: [{
                        name: 'blurValid'
                    }]
                });
            });

            $('#customThemeColorRows').on('click', '.remove-custom-theme-color', function(e) {
                e.preventDefault();
                $(this).closest('.custom-theme-color-row').remove();
            });

            var $customVariableRow = $('#customVariableRowData').find('.custom-variable-row');

            function customVariableRender(item, escape) {
                return item.optgroup ? '<div>' + escape(item.text) + ' (' + escape(item.optgroup) + ')</div>' : '<div>' + escape(item.text) + '</div>';
            }

            function selectizeVariableName($select) {
                $select.selectize({
                    create: true,
                    persist: false,
                    render: {
                        item: customVariableRender
                    }
                });
            }

            function applyCustomVariableValueType($row) {
                var name = $row.find('.custom-variable-name').val() || '';
                var $value = $row.find('.custom-variable-value');
                var isColor = /-(bg|background|color)$/.test(name) || /color/.test(name);
                if (isColor && !$value.data('colorpicker')) {
                    $value.colorpicker({
                        autoInputFallback: false,
                        autoHexInputFallback: false,
                        format: 'auto',
                        useAlpha: true,
                        extensions: [{
                            name: 'blurValid'
                        }]
                    });
                } else if (!isColor && $value.data('colorpicker')) {
                    $value.colorpicker('destroy');
                }
            }

            function initCustomVariableRow($row) {
                selectizeVariableName($row.find('.custom-variable-name'));
                applyCustomVariableValueType($row);
                $row.find('.custom-variable-name').on('change', function() {
                    applyCustomVariableValueType($row);
                });
            }

            $('#customVariableRows .custom-variable-row').each(function() {
                initCustomVariableRow($(this));
            });

            $('#addCustomVariable').on('click', function(e) {
                e.preventDefault();
                var $clone = $customVariableRow.clone();
                $('#customVariableRows').append($clone);
                initCustomVariableRow($clone);
            });

            $('#customVariableRows').on('click', '.remove-custom-variable', function(e) {
                e.preventDefault();
                $(this).closest('.custom-variable-row').remove();
            });

            var themeStepDefault = {{ (int) $stepDefault }};

            $(document).on('click', '.set-step-default', function(e) {
                e.preventDefault();
                $(this).closest('.form-group').find('input[name*="[step]"]').val(themeStepDefault);
            });

            $(document).on('click', '.clear-step', function(e) {
                e.preventDefault();
                $(this).closest('.form-group').find('input[name*="[step]"]').val('');
            });

            $('.set-all-steps').on('click', function(e) {
                e.preventDefault();
                $($(this).data('target')).find('input[name*="[step]"]').val(themeStepDefault);
            });

            $('.clear-all-steps').on('click', function(e) {
                e.preventDefault();
                $($(this).data('target')).find('input[name*="[step]"]').val('');
            });

            $('.delete-bootstrap-theme-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/bootstrap-themes/delete') }}/{{ $bootstrap->id }}", 'Delete Bootstrap Theme');
            });

            function styleFieldValue(key) {
                var $el = $('[name="style_data[' + key + ']"]');
                return $el.val() || $el.attr('placeholder') || '';
            }

            function refreshThumbnailPreview() {
                var width = styleFieldValue('thumbnail-border-width');
                if ($.isNumeric(width)) {
                    width += 'px';
                }
                $('#thumbnailPreview').css({
                    'background-color': styleFieldValue('thumbnail-bg'),
                    'border-width': width,
                    'border-style': 'solid',
                    'border-color': styleFieldValue('thumbnail-border-color'),
                    'border-radius': styleFieldValue('thumbnail-border-radius'),
                });
            }

            $('#refreshThumbnailPreview').on('click', refreshThumbnailPreview);
            refreshThumbnailPreview();

            function applyTooltipPreviewStyles() {
                var bg = styleFieldValue('tooltip-bg');
                $('#tooltipPreviewStyle').text(
                    '.tooltip-preview .tooltip-inner {' +
                    'background-color:' + bg + ';' +
                    'color:' + styleFieldValue('tooltip-color') + ';' +
                    'border-radius:' + styleFieldValue('tooltip-border-radius') + ';' +
                    '}' +
                    '.tooltip-preview.show { opacity:' + styleFieldValue('tooltip-opacity') + '; }' +
                    '.tooltip-preview .arrow::before {' +
                    'border-top-color:' + bg + ';' +
                    '}'
                );
            }

            var $tooltipPreviewBtn = $('#tooltipPreviewBtn');
            if ($tooltipPreviewBtn.length) {
                $tooltipPreviewBtn.tooltip({
                    trigger: 'manual',
                    placement: 'top',
                    container: 'body',
                    title: 'Tooltip preview',
                    template: '<div class="tooltip tooltip-preview bs-tooltip-top" role="tooltip" x-placement="top"><div class="arrow"></div><div class="tooltip-inner"></div></div>',
                });

                $tooltipPreviewBtn.on('click', function(e) {
                    e.stopPropagation();
                    applyTooltipPreviewStyles();
                    $tooltipPreviewBtn.tooltip('show');
                });

                $(document).on('click', function() {
                    $tooltipPreviewBtn.tooltip('hide');
                });
            }

            var $submitButtons = $('.bootstrap-theme-submit');
            var $form = $submitButtons.closest('form');
            var clickedAction = null;

            $submitButtons.on('click', function() {
                clickedAction = $(this).val();
            });

            $form.on('submit', function() {
                $submitButtons.prop('disabled', true).each(function() {
                    var $btn = $(this);
                    if ($btn.val() == clickedAction) {
                        $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>' + (clickedAction == 'save_compile' ? 'Compiling...' : 'Saving...'));
                    }
                });
                if (clickedAction) {
                    $form.append($('<input>', {
                        type: 'hidden',
                        name: 'action',
                        value: clickedAction
                    }));
                }
            });
        });
    </script>
@endsection
