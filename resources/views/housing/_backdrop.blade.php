@php
    $decor = $ownedDecor->decor;
@endphp

<div class="housing-backdrop housing-backdrop-{{ $slot }}" data-owned-decor-id="{{ $ownedDecor->id }}" style="position: absolute; inset: 0; z-index: {{ config('lorekeeper.housing.backdrop_z')[$slot] ?? 0 }};">
    @if ($decor->has_image)
        <img src="{{ $decor->decorImageUrl }}" alt="{{ $decor->name }}" style="width: 100%; height: 100%; display: block;">
    @endif

    @include('housing._mask_overlays', ['ownedDecor' => $ownedDecor])
</div>
