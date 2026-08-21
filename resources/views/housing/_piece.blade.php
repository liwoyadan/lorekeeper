@php
    $decor = $ownedDecor->decor;
@endphp

<div class="housing-piece" data-owned-decor-id="{{ $ownedDecor->id }}" data-layer="{{ $decor->layer }}" data-z="{{ $p['z'] ?? 0 }}" data-flip="{{ isset($p['flip_x']) && $p['flip_x'] ? 1 : 0 }}" style="position: absolute; left: {{ $p['x'] ?? 0 }}%; top: {{ $p['y'] ?? 0 }}%; width: {{ $p['scale'] ?? 20 }}%; z-index: {{ $p['z'] ?? 0 }}; transform: {{ isset($p['flip_x']) && $p['flip_x'] ? 'scaleX(-1)' : 'none' }};">
    @if ($decor->has_image)
        <img src="{{ $decor->decorImageUrl }}" alt="{{ $decor->name }}" style="width: 100%; height: auto; display: block;">
    @endif

    @include('housing._mask_overlays', ['ownedDecor' => $ownedDecor])
</div>
