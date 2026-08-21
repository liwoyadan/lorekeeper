@extends('world.layout')

@section('world-title')
    {{ $decor->name }}
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Housing' => 'world/housing', $decor->name => 'world/housing/' . $decor->id]) !!}

    <div class="card mb-3">
        <div class="card-body">
            <div class="row world-entry">
                @if ($decor->has_image)
                    <div class="col-md-3 world-entry-image">
                        <a href="{{ $decor->decorImageUrl }}" data-lightbox="entry" data-title="{{ $decor->name }}"><img src="{{ $decor->decorImageUrl }}" class="world-entry-image" alt="{{ $decor->name }}" /></a>
                    </div>
                @endif
                <div class="{{ $decor->has_image ? 'col-md-9' : 'col-12' }}">
                    <h1>
                        @if (!$decor->is_visible)
                            <i class="fas fa-eye-slash mr-1"></i>
                        @endif
                        {{ $decor->name }}
                    </h1>
                    <p><strong>Kind:</strong> {{ $decor->kindLabel }}@if ($decor->layer) &nbsp;&nbsp; <strong>Layer:</strong> {{ $decor->layerLabel }}@endif</p>
                    <div class="world-entry-text">{!! $decor->parsed_description !!}</div>
                </div>
            </div>

            @if ($decor->zones->count())
                <hr>
                <h3>Variations</h3>
                @foreach ($decor->zones as $zone)
                    <div class="mb-3">
                        <strong>{{ $zone->name }}</strong>
                        @if ($zone->allow_free_color)
                            <span class="badge badge-info ml-1">Free color allowed</span>
                        @endif
                        @if ($zone->colors->count() || $zone->patterns->count())
                            <div class="d-flex flex-wrap align-items-center mt-1" style="gap: 0.5rem;">
                                @foreach ($zone->colors as $color)
                                    <span title="#{{ $color->hex }}" style="display:inline-block; width:28px; height:28px; border-radius:4px; border:1px solid #ccc; background-color:#{{ $color->hex }};"></span>
                                @endforeach
                                @foreach ($zone->patterns as $pattern)
                                    <img src="{{ $pattern->patternImageUrl }}" alt="{{ $pattern->name }}" title="{{ $pattern->name }}" style="width:28px; height:28px; border-radius:4px; border:1px solid #ccc; object-fit:cover;">
                                @endforeach
                            </div>
                        @elseif (!$zone->allow_free_color)
                            <div class="text-muted small">No options.</div>
                        @endif
                    </div>
                @endforeach
            @endif

            <hr>
            <h3>How to obtain</h3>
            @if ($grantingItems->count())
                <div class="row">
                    @foreach ($grantingItems as $item)
                        <div class="col-md-4 mb-1"><a href="{{ $item->idUrl }}">{{ $item->name }}</a></div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">This decor is not currently granted by any item.</p>
            @endif
        </div>
    </div>
@endsection
