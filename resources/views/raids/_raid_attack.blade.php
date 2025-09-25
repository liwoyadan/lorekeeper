@if ($boss->health < 1)
    <div class="text-center">
        This boss has already been defeated!
    </div>
@else
    @if ($raid->damage)
        <div class="text-center">
            Requires <b>{{ $raid->attackAsset()['quantity'] }} {!! $raid->attackAsset()['asset']->displayName !!} ({{ $raid->damage['type'] }})</b> to attack.
        </div>
        @if ($raid->attackAsset()['asset']->imageUrl || $raid->attackAsset()['asset']->currencyImageUrl || $raid->attackAsset()['asset']->currencyIconUrl)
            <div class="text-center my-2">
                <img src="{{ ($raid->attackAsset()['asset']->imageUrl ?? $raid->attackAsset()['asset']->currencyImageUrl) ?? ($raid->attackAsset()['asset']->currencyIconUrl ?? null) }}" alt="{{ $raid->attackAsset()['asset']->name }}" class="img-fluid p-2 bg-secondary rounded-circle">
            </div>
        @endif
        <div class="text-center">
            @if (isset($raid->damage['max']))
                Between <b>{{ $raid->damage['base'] }}</b> to <b>{{ $raid->damage['max'] }}</b> points of damage per attack.
            @else
                <b>{{ $raid->damage['base'] }}</b> points of damage per attack.
            @endif
        </div>
        {!! Form::open(['url' => 'raids/attack/' . $raid->id .'/boss/' . $boss->id]) !!}

        <div class="text-center">
            {!! Form::submit('Attack!', ['class' => 'btn btn-primary font-weight-bold']) !!}
        </div>
        {!! Form::close() !!}
    @endif
@endif
