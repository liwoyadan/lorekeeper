@extends('admin.layout')

@section('admin-title')
    Bootstrap Themes
@endsection

@push('head')
    <style>
        .color-swatch {
            display: inline-block;
            width: 16px;
            height: 168px;
            border-radius: 3px;
            border: 1px solid rgba(0, 0, 0, 0.15);
            vertical-align: middle;
        }

        .color-swatches {
            white-space: nowrap;
        }
    </style>
@endpush

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Themes' => 'admin/themes', 'Bootstrap Themes' => 'admin/bootstrap-themes']) !!}

    <h1>Bootstrap Themes</h1>
    <p>
        Bootstrap themes are rethemed versions of Bootstrap v4 with custom variables, compiled into a single CSS file like the default app.css. Bootstrap themes are made/edited separately from regular themes intentionally - so a single rethemed Bootstrap
        can be applied to multiple themes.<br>
        The Bootstrap themes made here can then be applied to any <b>base</b> themes made with theme manager.
    </p>

    <div class="text-right mb-2">
        <a class="btn btn-primary" href="{{ url('admin/bootstrap-themes/create') }}">
            <i class="fas fa-plus"></i> Create New Bootstrap Theme
        </a>
    </div>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mb-2">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group ml-2 mb-2">
            {!! Form::select(
                'sort',
                [
                    'alpha' => 'Sort Alphabetically (A-Z)',
                    'alpha-reverse' => 'Sort Alphabetically (Z-A)',
                    'newest' => 'Newest First',
                    'oldest' => 'Oldest First',
                ],
                Request::get('sort') ?: 'alpha',
                ['class' => 'form-control'],
            ) !!}
        </div>
        <div class="form-group ml-2 mb-2">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    @if (!count($bootstrapThemes))
        <p>No bootstrap themes found.</p>
    @else
        @php
            $swatches = config('lorekeeper.themes.theme_colors');
        @endphp
        {!! $bootstrapThemes->render() !!}
        <div class="row no-gutters">
            <div class="row flex-wrap col-12 pb-1 ubt-bottom font-weight-bold">
                <div class="col-12 col-md-4">Name</div>
                <div class="col-6 col-md">Colors</div>
                <div class="col-6 col-md-2 text-center">Themes Using</div>
                <div class="col-6 col-md-2 text-center">Last Updated</div>
                <div class="col-6 col-md-1"></div>
            </div>
            @foreach ($bootstrapThemes as $bootstrap)
                @php
                    $colors = $bootstrap->color_data ?? [];
                @endphp
                <div class="row flex-wrap col-12 mt-1 pt-2 ubt-top align-items-center">
                    <div class="col-12 col-md-4">
                        {{ $bootstrap->name }}
                    </div>
                    <div class="col-6 col-md color-swatches">
                        @foreach ($swatches as $key => $entry)
                            @php
                                $isDefault = !isset($colors[$key]) || $colors[$key] == '';
                                $swatch = $isDefault ? $entry['default'] : $colors[$key];
                            @endphp
                            <span class="color-swatch mr-1" style="background-color: {{ $swatch }};" data-toggle="tooltip" title="{{ $entry['label'] }}: {{ $swatch }}{{ $isDefault ? ' (default)' : '' }}"></span>
                        @endforeach
                    </div>
                    <div class="col-6 col-md-2 text-center">
                        {{ $bootstrap->themes_count }}
                        {{ Str::plural('theme', $bootstrap->themes_count) }}
                    </div>
                    <div class="col-6 col-md-2 text-center">
                        <span class="small text-muted">{{ $bootstrap->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="col-6 col-md-1 text-right">
                        <a href="{{ url('admin/bootstrap-themes/edit/' . $bootstrap->id) }}" class="btn btn-primary py-0 px-2">Edit</a>
                    </div>
                </div>
            @endforeach
        </div>
        {!! $bootstrapThemes->render() !!}
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
