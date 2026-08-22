@php
    $fills = $ownedDecor->svgFills();
    $patternFills = $ownedDecor->svgPatternFills($fills);
@endphp

<div class="housing-svg-art" style="width: 100%; height: 100%;">
    {!! $ownedDecor->svgMarkup() !!}
</div>

@if (count($patternFills))
    <svg width="0" height="0" style="position: absolute;" aria-hidden="true">
        <defs>
            @foreach ($patternFills as $f)
                <pattern id="{{ $f['patternDefId'] }}" patternUnits="userSpaceOnUse" width="60" height="60">
                    <image href="{{ $f['patternUrl'] }}" x="0" y="0" width="60" height="60" preserveAspectRatio="xMidYMid slice"></image>
                </pattern>
            @endforeach
        </defs>
    </svg>
@endif

@if (count($fills))
    <style>
        [data-owned-decor-id="{{ $ownedDecor->id }}"] .housing-svg-art svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        @foreach ($fills as $f)
            [data-owned-decor-id="{{ $ownedDecor->id }}"] {!! $f['selector'] !!} {
                fill: {{ $f['fill'] }};
            }
        @endforeach
    </style>
@endif
