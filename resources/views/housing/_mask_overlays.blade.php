@php
    $decor = $ownedDecor->decor;
@endphp

@if ($decor->render_mode == 'mask')
    @foreach ($decor->zones as $zone)
        @php
            $fill = $ownedDecor->zoneFill($zone);
        @endphp
        @if ($fill)
            <div
                style="position: absolute; inset: 0; -webkit-mask-image: url({{ $zone->maskUrl }}); mask-image: url({{ $zone->maskUrl }}); -webkit-mask-size: 100% 100%; mask-size: 100% 100%; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; {{ $fill }}">
            </div>
        @endif
    @endforeach
@endif
