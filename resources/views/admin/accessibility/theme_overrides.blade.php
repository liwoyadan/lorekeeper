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
            <div class="row no-gutters">
                <div class="row flex-wrap col-12 pb-1 ubt-bottom font-weight-bold">
                    <div class="col-12 col-md-5">Setting</div>
                    <div class="col-6 col-md-3 text-center">On Theme(s)...</div>
                    <div class="col-6 col-md-4">Theme Default</div>
                </div>
                @foreach ($settings as $setting)
                    @php
                        $entry = $themeData[$setting->setting_key] ?? [];
                        $enabled = $entry['is_enabled'] ?? true;
                    @endphp
                    <div class="row flex-wrap col-12 mt-1 pt-2 ubt-top align-items-center">
                        <div class="col-12 col-md-5">
                            <b>{{ $setting->name }}</b>
                            <span class="badge badge-secondary ml-1">{{ ucfirst($setting->input_type) }}</span><br>
                            <code class="small">{{ $setting->setting_key }}</code>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            {!! Form::hidden('overrides[' . $setting->setting_key . '][is_enabled]', 0) !!}
                            {!! Form::checkbox('overrides[' . $setting->setting_key . '][is_enabled]', 1, $enabled, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => 'Offered', 'data-off' => 'Hidden', 'data-onstyle' => 'success', 'data-offstyle' => 'secondary']) !!}
                        </div>
                        <div class="col-6 col-md-4">
                            {!! Form::text('overrides[' . $setting->setting_key . '][default_value]', $entry['default_value'] ?? null, ['class' => 'form-control', 'placeholder' => $setting->default_value ?? 'global default']) !!}
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-right mt-3">
                {!! Form::submit('Save Theme Overrides', ['class' => 'btn btn-primary']) !!}
            </div>
        @endif
        {!! Form::close() !!}
    @endif
@endsection
