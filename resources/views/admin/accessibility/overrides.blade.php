@extends('admin.layout')

@section('admin-title')
    Accessibility Selector Overrides
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Themes' => 'admin/themes',
        'Accessibility Settings' => 'admin/accessibility-settings',
        'Selector Overrides' => 'admin/accessibility-settings/overrides',
    ]) !!}

    <h1>
        Selector Overrides
    </h1>
    <p>
        Each target comes with a default CSS selector and property adjusted for Lorekeeper's default styling. If your site's styling differs, override the selector (and optionally, the property itself) here, generally only needs to be once per site. (Unless if you make changes to your styling, have alternate layouts with meaningful differences, etc.) Leave a field blank to keep the config file default shown as the placeholder. These apply site-wide, across every theme.
    </p>

    {!! Form::open(['url' => 'admin/accessibility-settings/overrides']) !!}
    <div class="row no-gutters">
        <div class="row flex-wrap col-12 pb-1 ubt-bottom font-weight-bold">
            <div class="col-12 col-md-3">Target</div>
            <div class="col-12 col-md-5">Selector</div>
            <div class="col-12 col-md-4">Property</div>
        </div>
        @foreach ($catalog as $key => $entry)
            @php $ov = $overrides[$key] ?? null; @endphp
            <div class="row flex-wrap col-12 mt-1 pt-2 ubt-top align-items-center">
                <div class="col-12 col-md-3">
                    <b>{{ $entry['label'] }}</b><br>
                    <code class="small">{{ $key }}</code>
                </div>
                <div class="col-12 col-md-5">
                    {!! Form::text('overrides[' . $key . '][selector]', $ov->selector ?? null, ['class' => 'form-control', 'placeholder' => $entry['selector'] ?? '']) !!}
                </div>
                <div class="col-12 col-md-4">
                    {!! Form::text('overrides[' . $key . '][property]', $ov->property ?? null, ['class' => 'form-control', 'placeholder' => $entry['property'] ?? '']) !!}
                </div>
            </div>
        @endforeach
    </div>
    <div class="text-right mt-3">
        {!! Form::submit('Save Overrides', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}
@endsection
