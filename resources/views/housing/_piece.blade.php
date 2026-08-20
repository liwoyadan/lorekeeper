@php
    $decor = $ownedDecor->decor;
@endphp

<div class="housing-piece" style="position: absolute; left: {{ $p['x'] ?? 0 }}%; top: {{ $p['y'] ?? 0 }}%; width: {{ $p['scale'] ?? 20 }}%; z-index: {{ $p['z'] ?? 0 }}; transform: {{ isset($p['flip_x']) && $p['flip_x'] ? 'scaleX(-1)' : 'none' }};">
    @if ($decor->has_image)
        <img src="{{ $decor->decorImageUrl }}" alt="{{ $decor->name }}" style="width: 100%; height: auto; display: block;">
    @endif

    @if ($decor->render_mode == 'mask')
        @foreach ($decor->zones as $zone)
            @php
                $fill = $ownedDecor->zoneFill($zone);
            @endphp
            @if ($fill)
                <div style="position: absolute; inset: 0; -webkit-mask-image: url({{ $zone->maskUrl }}); mask-image: url({{ $zone->maskUrl }}); -webkit-mask-size: 100% 100%; mask-size: 100% 100%; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; {{ $fill }}"></div>
            @endif
        @endforeach
    @endif
</div>
