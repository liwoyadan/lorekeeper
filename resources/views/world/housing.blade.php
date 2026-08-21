@extends('world.layout')

@section('world-title')
    Housing Decor
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Housing' => 'world/housing']) !!}
    <h1>Housing Decor</h1>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select('kind', $kinds, Request::get('kind'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select('layer', $layers, Request::get('layer'), ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
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
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    {!! $decors->render() !!}
    @foreach ($decors as $decor)
        <div class="card mb-3">
            <div class="card-body">
                <div class="row world-entry">
                    @if ($decor->has_image)
                        <div class="col-md-3 world-entry-image">
                            <a href="{{ url('world/housing/' . $decor->id) }}"><img src="{{ $decor->decorImageUrl }}" class="world-entry-image" alt="{{ $decor->name }}" /></a>
                        </div>
                    @endif
                    <div class="{{ $decor->has_image ? 'col-md-9' : 'col-12' }}">
                        <h3>
                            @if (!$decor->is_visible)
                                <i class="fas fa-eye-slash mr-1"></i>
                            @endif
                            <a href="{{ url('world/housing/' . $decor->id) }}">{{ $decor->name }}</a>
                        </h3>
                        <p class="text-muted mb-1">{{ $decor->kindLabel }}@if ($decor->layer) &middot; {{ $decor->layerLabel }}@endif</p>
                        <div class="world-entry-text">{!! $decor->parsed_description !!}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    {!! $decors->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $decors->total() }} result{{ $decors->total() == 1 ? '' : 's' }} found.</div>
@endsection
