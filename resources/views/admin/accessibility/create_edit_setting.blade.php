@extends('admin.layout')

@section('admin-title')
    {{ $setting->id ? 'Edit Accessibility Setting' : 'Create Accessibility Setting' }}
@endsection

@section('admin-content')
    @php
        $catalogMeta = [];
        $keyOptions = [];
        foreach ($catalog as $key => $entry) {
            $catalogMeta[$key] = [
                'input_type' => $entry['input_type'],
                'selector' => $entry['selector'] ?? (isset($entry['levels']) ? '#main headings (h1 to h6)' : ''),
                'property' => $entry['property'] ?? (isset($entry['levels']) ? 'font-size' : ''),
                'has_bases' => isset($entry['levels']),
                'bases' => isset($entry['levels'])
                    ? collect($entry['levels'])
                        ->map(function ($lvl) {
                            return $lvl['base'] ?? '';
                        })
                        ->toArray()
                    : [],
            ];
            $keyOptions[$key] = $entry['label'] . ' (' . $key . ')';
        }
    @endphp

    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Themes' => 'admin/themes',
        'Accessibility Settings' => 'admin/accessibility-settings',
        ($setting->id ? 'Edit' : 'Create') . ' Setting' => $setting->id ? 'admin/accessibility-settings/edit/' . $setting->id : 'admin/accessibility-settings/create',
    ]) !!}

    <h1>
        {{ $setting->id ? 'Edit Accessibility Setting' : 'Create Accessibility Setting' }}
        @if ($setting->id)
            <a href="#" class="btn btn-danger float-right delete-setting-button">
                Delete Setting
            </a>
        @endif
    </h1>

    {!! Form::open(['url' => $setting->id ? 'admin/accessibility-settings/edit/' . $setting->id : 'admin/accessibility-settings/create', 'id' => 'settingForm']) !!}
    <div class="row">
        <div class="col-md">
            <div class="form-group">
                {!! Form::label('name', 'Name') !!}
                {!! Form::text('name', $setting->name, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="col-md">
            <div class="form-group">
                {!! Form::label('setting_key', 'Target') !!}
                {!! add_help(
                    'The target this setting uses. It sets the CSS selector, property and input type, and <b>cannot be changed</b> after creation. (You will have to delete and re-create.) If this site has custom styling or you would otherwise like to change it, you can do so on the <b>overrides page</b> or the <b>config file.</b>',
                ) !!}
                @if ($setting->id)
                    {!! Form::hidden('setting_key', $setting->setting_key) !!}
                    {!! Form::text('setting_key_label', ($catalog[$setting->setting_key]['label'] ?? $setting->setting_key) . ' (' . $setting->setting_key . ')', ['class' => 'form-control', 'disabled']) !!}
                @else
                    {!! Form::select('setting_key', $keyOptions, null, ['class' => 'form-control selectize', 'id' => 'setting_key', 'placeholder' => 'Select a Target']) !!}
                @endif
            </div>
        </div>
    </div>

    <div class="alert border border-primary" id="targetHint" style="display: none;">
        Applies <code id="hintProperty"></code> to <code id="hintSelector"></code>.
    </div>

    <div class="form-group">
        {!! Form::label('description', 'Description (optional)') !!} {!! add_help('An optional description that will display alongside the setting\'s name in the settings panel.') !!}
        {!! Form::textarea('description', $setting->description, ['class' => 'form-control', 'rows' => 2]) !!}
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            {!! Form::label('panel_key', 'Panel') !!} {!! add_help('Settings are grouped together by panel. All settings you place under X group will show up together.') !!}
            {!! Form::select('panel_key', $panels, $setting->panel_key, ['class' => 'form-control selectize', 'placeholder' => 'Select Panel (Group)']) !!}
        </div>
        <div class="form-group col-md-3">
            {!! Form::label('sort_order', 'Sort Order') !!}
            {!! Form::number('sort_order', $setting->sort_order ?? 0, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group col-md-3">
            {!! Form::label('default_value', 'Default (optional)') !!}
            {!! Form::text('default_value', $setting->default_value, ['class' => 'form-control']) !!}
        </div>
    </div>

    <hr>
    <h4>Options</h4>
    @include('admin.accessibility._input_options')
    <hr>

    <div class="row mt-3">
        <div class="form-group col-md" id="constrainedField">
            <div class="form-check">
                {!! Form::checkbox('is_constrained', 1, $setting->is_constrained, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'is_constrained']) !!}
                {!! Form::label('is_constrained', 'Constrained (Set Choices)', ['class' => 'form-check-label ml-1']) !!}{!! add_help('When on, users can <b>only</b> choose from the <b>preset choices</b> you define rather than any free value. Select inputs are <b>always</b> constrained; colour input will be a colourpicker if not set as constrained.') !!}
            </div>
        </div>
        <div class="form-group col-md">
            <div class="form-check">
                {!! Form::checkbox('is_active', 1, $setting->id ? $setting->is_active : true, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'is_active']) !!}
                {!! Form::label('is_active', 'Active', ['class' => 'form-check-label ml-1']) !!}{!! add_help('Whether or not this setting is available for users at the moment.') !!}
            </div>
        </div>
    </div>

    <div class="text-right">
        {!! Form::submit($setting->id ? 'Save' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}
@endsection

@section('scripts')
    @parent
    <script>
        var a11yCatalog = {{ Js::from($catalogMeta) }};
        $('.selectize').selectize();

        function a11yCurrentKey() {
            var select = $('#setting_key');
            return select.length ? select.val() : '{{ $setting->setting_key }}';
        }

        function a11ySyncOptions() {
            var meta = a11yCatalog[a11yCurrentKey()];
            var type = meta ? meta.input_type : null;

            $('.a11y-options').each(function() {
                var types = ($(this).data('types') || '').toString().split(',');
                var match = !!type && types.indexOf(type) != -1;
                $(this).toggle(match);
                $(this).find('input, select, textarea').prop('disabled', !match);
            });

            // heading-scale targets carry per-number base sizes
            var showBases = !!(meta && meta.has_bases);
            $('.a11y-bases').toggle(showBases);
            $('.a11y-bases').find('input').prop('disabled', !showBases);
            if (showBases && meta.bases) {
                $('.a11y-bases').find('input').each(function() {
                    var lvl = $(this).data('level');
                    if (meta.bases[lvl] != undefined && meta.bases[lvl] != '') {
                        $(this).attr('placeholder', meta.bases[lvl]);
                    }
                });
            }

            // constrained only means anything for colour (swatches vs free colourpicker); everything else it's ignored
            $('#constrainedField').toggle(type == 'color');

            if (meta) {
                $('#hintProperty').text(meta.property);
                $('#hintSelector').text(meta.selector);
                $('#targetHint').show();
            } else {
                $('#targetHint').hide();
            }
        }

        $(document).ready(function() {
            a11ySyncOptions();
            $('#setting_key').on('change', a11ySyncOptions);

            $('.a11y-add-row').on('click', function(e) {
                e.preventDefault();
                var field = $(this).data('field');
                var clone = $('#' + field + 'Clone');
                var row = clone.clone().removeClass('a11y-clone hide').removeAttr('id');
                row.find('[name]').prop('disabled', false);
                $('#' + field + 'List').append(row);
                if (field == 'presets') {
                    row.find('.cp').colorpicker({
                        'autoInputFallback': false,
                        'autoHexInputFallback': false,
                        'format': 'auto',
                        'useAlpha': true,
                        extensions: [{
                            name: 'blurValid'
                        }]
                    });
                }
            });

            $(document).on('click', '.a11y-remove-row', function(e) {
                e.preventDefault();
                $(this).closest('.a11y-choice-row').remove();
            });

            $('.delete-setting-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/accessibility-settings/delete') }}/{{ $setting->id }}", 'Delete Setting');
            });
        });
    </script>
@endsection
