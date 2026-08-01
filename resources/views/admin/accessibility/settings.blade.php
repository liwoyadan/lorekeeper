@extends('admin.layout')

@section('admin-title')
    Accessibility Settings
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Themes' => 'admin/themes', 'Accessibility Settings' => 'admin/accessibility-settings']) !!}

    <h1>
        Accessibility Settings
    </h1>
    <p>
        Accessibility (or just alt/alternative) settings are options that users adjust for themselves (font size, colours, spacing, motion, etc). Similar to picking a theme, but only to adjust specific individual aspects of the site such as the size of
        text! Defaults are written in a config file at <code>config/lorekeeper/themes.php</code>.<br>
        Each setting maps to a target from the accessibility config file catalog; you can choose which target a setting uses, name it, and decide how users pick a value (i.e. only select from a preset list of options or enter any value). If your site's
        styling is customized past the Lorekeeper default, remap a target's selector on the <a href="{{ url('admin/accessibility-settings/overrides') }}" class="font-weight-bold">selector overrides</a> page, and tune per-theme defaults on the <a
            href="{{ url('admin/accessibility-settings/themes') }}" class="font-weight-bold">theme overrides</a> page!<br>
        You can also edit the config file to add, remove, and adjust as you see fit.
    </p>

    @if (!Settings::get('accessibility_menu_enabled'))
        <div class="alert alert-primary text-center">
            The accessibility menu is currently disabled, so users will not see it in the site navigation, but you can still create and manage accessibility/alternate settings here.<br>
            Set the <code>accessibility_menu_enabled</code> site setting to <code>1</code> to make the menu available to users over at the <a href="{{ url('admin/settings') }}" class="font-weight-bold">site settings page</a>.
        </div>
    @endif

    <div class="text-right mb-3">
        <a class="btn btn-outline-primary mr-1" href="{{ url('admin/accessibility-settings/overrides') }}">
            <i class="fas fa-crosshairs" aria-hidden="true"></i> Selector Overrides
        </a>
        <a class="btn btn-outline-primary mr-1" href="{{ url('admin/accessibility-settings/themes') }}">
            <i class="fas fa-palette" aria-hidden="true"></i> Theme Overrides
        </a>
        <a class="btn btn-primary" href="{{ url('admin/accessibility-settings/create') }}">
            <i class="fas fa-plus" aria-hidden="true"></i> Create New Setting
        </a>
    </div>

    @if (!count($settings))
        <p>
            No accessibility/alt settings have been created yet.
        </p>
    @else
        <div class="row no-gutters flex-wrap col-12 pb-1 font-weight-bold">
            <div class="col-12 col-md">Name</div>
            <div class="col-6 col-md-3">Target</div>
            <div class="col-6 col-md-3">Panel Group</div>
            <div class="col-6 col-md-1"></div>
        </div>
        @foreach ($settings as $setting)
            <div class="row no-gutters flex-wrap col-12 mt-1 py-1 ubt-top align-items-center">
                <div class="col-12 col-md">
                    @if ($setting->is_active)
                        <i class="fas fa-check-circle text-success" data-toggle="tooltip" title="This setting is currently active."></i>
                    @else
                        <i class="fas fa-times-circle text-secondary" data-toggle="tooltip" title="This setting is currently inactive and will not show as an option on the menu."></i>
                    @endif

                    {{ $setting->name }}
                    <span class="badge badge-primary">
                        {{ ucfirst($setting->input_type) }}
                    </span>

                    @if ($setting->is_constrained)
                        <span class="badge badge-info">Constrained</span>
                    @endif
                </div>

                <div class="col-6 col-md-3">
                    <code>
                        {{ $setting->setting_key }}
                    </code>
                    @if (!isset($catalog[$setting->setting_key]))
                        <span class="badge badge-danger text-uppercase" data-toggle="tooltip" title="This setting_key is no longer in the config file catalog.">
                            <i class="fas fa-exclamation-triangle" aria-hidden="true"></i> Missing
                        </span>
                    @endif
                </div>

                <div class="col-6 col-md-3">
                    {{ $panels[$setting->panel_key] ?? $setting->panel_key }}
                </div>


                <div class="col-6 col-md-1 text-right">
                    <a href="{{ url('admin/accessibility-settings/edit/' . $setting->id) }}" class="btn btn-primary py-0 px-2">Edit</a>
                </div>
            </div>
        @endforeach
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
