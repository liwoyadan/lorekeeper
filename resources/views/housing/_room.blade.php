@php
    $byLayer = $home->placementsByLayer();
@endphp

<div class="housing-room mx-auto" style="position: relative; width: 100%; max-width: 960px; aspect-ratio: {{ config('lorekeeper.housing.stage_ratio') }}; background: #f8f9fa; border: 1px solid #dee2e6; overflow: hidden;">
    @foreach (['back', 'mid', 'front'] as $layer)
        <div class="housing-layer housing-layer-{{ $layer }}" style="position: absolute; inset: 0;">
            @foreach (($byLayer[$layer] ?? collect())->sortBy('placement.z') as $item)
                @include('housing._piece', ['p' => $item['placement'], 'ownedDecor' => $item['ownedDecor']])
            @endforeach
        </div>
    @endforeach
</div>

@if ($byLayer->isEmpty())
    <p class="text-center text-muted mt-2">This room is empty.</p>
@endif
