<div class="raid-display" style="{!! $raid->imageUrl ? "background-image: url('" . $raid->imageUrl . "');" : "" !!}">
    <div class="h-100 d-flex flex-column justify-content-center">
        <div class="{{ $heading ?? 'h5' }} mb-0">
            {!! $raidBoss->displayName !!}
        </div>
        @if ($raidBoss->imageUrl)
            <div class="boss-image d-flex flex-column flex-grow-1 justify-content-center">
                <div>
                    <img src="{{ $raidBoss->imageUrl }}" class="img-fluid" alt="Image of the current boss for raid {{ $raid->name }}">
                </div>
            </div>
        @endif
        @include('widgets.raids._raid_boss_health', ['raidBoss' => $raidBoss])
    </div>
</div>
