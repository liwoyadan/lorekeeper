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
        Accessibility (or just alt) settings are options that users adjust for themselves (font size, colours, spacing, motion, etc). Each setting maps to a target from the accessibility config file catalog; you can choose which target a setting uses, name it, and decide how users pick a value (i.e. only select from a preset list of options or enter any value). If your site's styling is customized past the Lorekeeper default, remap a target's selector on the <a href="{{ url('admin/accessibility-settings/overrides') }}">selector overrides</a> page, and tune per-theme defaults on the <a href="{{ url('admin/accessibility-settings/themes') }}">theme overrides</a> page!<br>
        You can also edit the config file to add, remove, and adjust as you see fit.
    </p>

    <div class="text-right mb-2">
        <a class="btn btn-outline-primary mr-1" href="{{ url('admin/accessibility-settings/overrides') }}">
            <i class="fas fa-crosshairs"></i> Selector Overrides
        </a>
        <a class="btn btn-outline-primary mr-1" href="{{ url('admin/accessibility-settings/themes') }}">
            <i class="fas fa-palette"></i> Theme Overrides
        </a>
        <a class="btn btn-primary" href="{{ url('admin/accessibility-settings/create') }}">
            <i class="fas fa-plus"></i> Create New Setting
        </a>
    </div>

    @if (!count($settings))
        <p>
            No accessibility/alt settings have been created yet.
        </p>
    @else
        <div class="row no-gutters">
            <div class="row flex-wrap col-12 pb-1 ubt-bottom font-weight-bold">
                <div class="col-12 col-md-4">Name</div>
                <div class="col-6 col-md-3">Target</div>
                <div class="col-6 col-md-2">Panel</div>
                <div class="col-6 col-md-2 text-center">Status</div>
                <div class="col-6 col-md-1"></div>
            </div>
            @foreach ($settings as $setting)
                <div class="row flex-wrap col-12 mt-1 pt-2 ubt-top align-items-center">
                    <div class="col-12 col-md-4">
                        {{ $setting->name }}
                        <span class="badge badge-secondary ml-1">{{ ucfirst($setting->input_type) }}</span>
                        @if ($setting->is_constrained)
                            <span class="badge badge-info">Constrained</span>
                        @endif
                    </div>
                    <div class="col-6 col-md-3">
                        <code>{{ $setting->setting_key }}</code>
                        @if (!isset($catalog[$setting->setting_key]))
                            <span class="badge badge-danger" data-toggle="tooltip" title="This setting_key is no longer in the config file catalog.">!! Missing</span>
                        @endif
                    </div>
                    <div class="col-6 col-md-2">
                        {{ $panels[$setting->panel_key] ?? $setting->panel_key }}
                    </div>
                    <div class="col-6 col-md-2 text-center">
                        @if ($setting->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>
                    <div class="col-6 col-md-1 text-right">
                        <a href="{{ url('admin/accessibility-settings/edit/' . $setting->id) }}" class="btn btn-primary py-0 px-2">Edit</a>
                    </div>
                </div>
            @endforeach
        </div>
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
