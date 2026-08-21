@php
    $byLayer = $home->placementsByLayer();
    $backdrops = $home->backdrops();
@endphp

<div class="housing-room mx-auto" style="position: relative; width: 100%; max-width: 960px; aspect-ratio: {{ config('lorekeeper.housing.stage_ratio') }}; background: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
    @foreach (['wall', 'floor'] as $slot)
        @if ($backdrops[$slot])
            @include('housing._backdrop', ['slot' => $slot, 'ownedDecor' => $backdrops[$slot]])
        @endif
    @endforeach
    @foreach (['back', 'mid', 'front'] as $layer)
        <div class="housing-layer housing-layer-{{ $layer }}" style="position: absolute; inset: 0; z-index: {{ $loop->index + 3 }};">
            @foreach (($byLayer[$layer] ?? collect())->sortBy('placement.z') as $item)
                @include('housing._piece', ['p' => $item['placement'], 'ownedDecor' => $item['ownedDecor']])
            @endforeach
        </div>
    @endforeach
</div>

@if ($byLayer->isEmpty() && !$backdrops['wall'] && !$backdrops['floor'])
    <p class="text-center text-muted mt-2">This room is empty.</p>
@endif
