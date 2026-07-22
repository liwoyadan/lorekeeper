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
                'selector'   => $entry['selector'] ?? '',
                'property'   => $entry['property'] ?? '',
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
            <a href="#" class="btn btn-danger float-right delete-setting-button">Delete Setting</a>
        @endif
    </h1>

    {!! Form::open(['url' => $setting->id ? 'admin/accessibility-settings/edit/' . $setting->id : 'admin/accessibility-settings/create', 'id' => 'settingForm']) !!}

    <div class="form-group">
        {!! Form::label('setting_key', 'Target') !!}
        {!! add_help('The target this setting uses. It fixes the CSS selector, property and input type, and cannot be changed after creation.') !!}
        @if ($setting->id)
            {!! Form::hidden('setting_key', $setting->setting_key) !!}
            {!! Form::text('setting_key_label', ($catalog[$setting->setting_key]['label'] ?? $setting->setting_key) . ' (' . $setting->setting_key . ')', ['class' => 'form-control', 'disabled']) !!}
        @else
            {!! Form::select('setting_key', ['' => 'Select a target'] + $keyOptions, null, ['class' => 'form-control', 'id' => 'setting_key']) !!}
        @endif
    </div>

    <div class="alert alert-secondary" id="targetHint" style="display: none;">
        Applies <code id="hintProperty"></code> to <code id="hintSelector"></code>.
    </div>

    <div class="form-group">
        {!! Form::label('name', 'Name') !!}
        {!! Form::text('name', $setting->name, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('description', 'Description (optional)') !!}
        {!! Form::textarea('description', $setting->description, ['class' => 'form-control', 'rows' => 2]) !!}
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            {!! Form::label('panel_key', 'Panel') !!}
            {!! Form::select('panel_key', $panels, $setting->panel_key, ['class' => 'form-control']) !!}
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

    <h5>Options</h5>
    @include('admin.accessibility._input_options')

    <div class="form-group mt-3 text-right">
        <div class="form-check">
            {!! Form::checkbox('is_constrained', 1, $setting->is_constrained, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'is_constrained']) !!}
            {!! Form::label('is_constrained', 'Constrained (Preset Choices)', ['class' => 'form-check-label ml-3']) !!}
            {!! add_help('When on, users can only choose from the preset choices you define rather than any free value. Select inputs are <i>always</i> constrained; colour input will be a colourpicker if not set as constrained.') !!}
        </div>
    </div>
    <div class="form-group text-right">
        <div class="form-check">
            {!! Form::checkbox('is_active', 1, $setting->id ? $setting->is_active : true, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'is_active']) !!}
            {!! Form::label('is_active', 'Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Whether or not this setting is available for users at the moment.') !!}
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
        var a11yRowIndex = 100000;

        function a11yCurrentKey() {
            var select = $('#setting_key');
            return select.length ? select.val() : '{{ $setting->setting_key }}';
        }

        function a11ySyncOptions() {
            var meta = a11yCatalog[a11yCurrentKey()];
            var type = meta ? meta.input_type : null;

            $('.a11y-options').each(function() {
                var types = ($(this).data('types') || '').toString().split(',');
                var match = type && types.indexOf(type) !== -1;
                $(this).toggle(match);
                $(this).find('input, select, textarea').prop('disabled', !match);
            });

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
                var proto = $('#' + field + 'Proto');
                var row = proto.clone().removeClass('a11y-proto hide').removeAttr('id');
                row.find('[name]').each(function() {
                    $(this).attr('name', $(this).attr('name').replace('__INDEX__', a11yRowIndex));
                    $(this).prop('disabled', false);
                });
                a11yRowIndex++;
                $('#' + field + 'List').append(row);
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
