@extends('admin.layout')

@section('admin-title')
    Accessibility Theme Overrides
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Themes' => 'admin/themes',
        'Accessibility Settings' => 'admin/accessibility-settings',
        'Theme Overrides' => 'admin/accessibility-settings/themes',
    ]) !!}

    <h1>
        Per-Theme Overrides
    </h1>
    <p>
        Set accessibility/alt settings per-theme: choose if each different setting is offered while a specific theme is active, and also give it a theme-specific default.<br>
        When a user saves a value for accessibility/alt settings, that value will <i>always</i> override theme & site defaults. Theme & site defaults only apply to users who have not set anything to override them.
    </p>

    {!! Form::open(['method' => 'GET', 'class' => 'form-inline mb-3']) !!}
    {!! Form::select('theme', ['' => 'Select a theme…'] + $themes->pluck('name', 'id')->toArray(), $theme->id ?? null, ['class' => 'form-control', 'onchange' => 'this.form.submit()']) !!}
    {!! Form::close() !!}

    @if ($theme)
        @php $themeData = $theme->accessibility_data ?? []; @endphp
        {!! Form::open(['url' => 'admin/accessibility-settings/themes/' . $theme->id]) !!}
        @if (!count($settings))
            <p>
                No accessibility/alt settings exist yet. Create some first!
            </p>
        @else
            <p class="text-muted mb-2">
                Settings already overridden on this theme are expanded. Hit <b>Customize</b> to override a setting; leave one collapsed to keep using its global options here.
            </p>
            @foreach ($settings as $setting)
                @include('admin.accessibility._theme_override_row', ['setting' => $setting, 'entry' => $themeData[$setting->setting_key] ?? []])
            @endforeach
            <div class="text-right mt-3">
                {!! Form::submit('Save Theme Overrides', ['class' => 'btn btn-primary']) !!}
            </div>
        @endif
        {!! Form::close() !!}
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function () {
            $('.a11y-theme-toggle').on('click', function (e) {
                e.preventDefault();
                var body = $(this).closest('.a11y-theme-row').find('.a11y-theme-body');
                body.toggleClass('hide');
                var open = !body.hasClass('hide');
                $(this).find('i').toggleClass('fa-chevron-down', !open).toggleClass('fa-chevron-up', open);
                $(this).find('.a11y-theme-toggle-text').text(open ? 'Hide' : 'Customize');
                if (open) {
                    // toggles that auto-init while hidden come out zero-width; rebuild once visible
                    var tog = body.find('input[data-toggle="toggle"]');
                    try { tog.bootstrapToggle('destroy'); } catch (err) {}
                    tog.bootstrapToggle();
                }
            });

            $('.a11y-add-row').on('click', function (e) {
                e.preventDefault();
                var field = $(this).data('field');
                var key = $(this).data('key');
                var clone = $('#' + field + 'Clone-' + key);
                var row = clone.clone().removeClass('a11y-clone hide').removeAttr('id');
                row.find('[name]').prop('disabled', false);
                $('#' + field + 'List-' + key).append(row);
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

            $(document).on('click', '.a11y-remove-row', function (e) {
                e.preventDefault();
                $(this).closest('.a11y-choice-row').remove();
            });
        });
    </script>
@endsection
