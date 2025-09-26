@if ($boss->damage >= $boss->health)
    <div class="text-center">
        This {{ __('raids.boss') }} has already been defeated!
    </div>
    <div class="alert alert-{{ $raid->logs()->where('user_id', Auth::user()->id)->first() ? 'success' : 'primary' }} text-center mt-2">
        @if ($raid->logs()->where('user_id', Auth::user()->id)->first())
            <b>You participated in this {{ __('raids.raid') }}!</b> Please wait for rewards to be distributed.
            @if ($raid->userDamage(Auth::user() ?? null) && $raid->userDamage(Auth::user() ?? null) >= 0)
                <br>
                You have dealt a total of <b>{{ $raid->userDamage(Auth::user() ?? null) }} damage</b> to this {{ __('raids.raid').' '.__('raids.boss') }}.
            @endif
        @else
            You didn't participate in this {{ __('raids.raid') }}.
        @endif
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
        @if ($raid->userDamage(Auth::user() ?? null) && $raid->userDamage(Auth::user() ?? null) >= 0)
            <div class="text-center">
                You have dealt <b>{{ $raid->userDamage(Auth::user() ?? null) }} damage</b> to this {{ __('raids.raid').' '.__('raids.boss') }} so far.
            </div>
        @endif

        @if ($raid->canAttack(Auth::user() ?? null))
            {!! Form::open(['url' => __('raids.raids').'/attack/' . $raid->id .'/'.__('raids.boss').'/' . $boss->id, 'class' => 'mt-2']) !!}
            <div class="text-center">
                {!! Form::submit('Attack!', ['class' => 'btn btn-primary font-weight-bold']) !!}
            </div>
            {!! Form::close() !!}
        @else
            <div class="alert alert-danger text-center mt-2">
                You lack the requirements to make an attack.
            </div>
        @endif
    @endif
@endif
