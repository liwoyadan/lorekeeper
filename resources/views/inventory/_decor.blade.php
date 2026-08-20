@php
    $grantedDecor = \App\Models\Housing\HousingDecor::with(['zones.patterns', 'zones.colors'])->find($tag->getData()['decor_id'] ?? null);
@endphp

@if ($grantedDecor)
    <li class="list-group-item">
        <a class="card-title h5 collapse-title" data-toggle="collapse" href="#redeemDecorForm">Redeem Decor</a>
        <div id="redeemDecorForm" class="collapse">
            {!! Form::hidden('tag', $tag->tag) !!}
            <p>Customize <strong>{{ $grantedDecor->name }}</strong>, then redeem. Your choices are locked once redeemed.</p>

            @foreach ($grantedDecor->zones as $zone)
                <div class="border rounded p-2 mb-2">
                    <strong>{{ $zone->name }}</strong>

                    @if (!$zone->colors->count() && !$zone->patterns->count() && !$zone->allow_free_color)
                        <div class="text-muted small">This zone is not recolorable.</div>
                    @else
                        @foreach ($zone->colors as $color)
                            <div class="form-check">
                                {!! Form::radio('zone_choice['.$zone->id.']', 'color:'.$color->hex, $loop->first, ['class' => 'form-check-input']) !!}
                                <label class="form-check-label">
                                    <span class="d-inline-block border" style="width:16px; height:16px; background:#{{ $color->hex }}; vertical-align:middle;"></span>
                                    #{{ $color->hex }}
                                </label>
                            </div>
                        @endforeach

                        @foreach ($zone->patterns as $pattern)
                            <div class="form-check">
                                {!! Form::radio('zone_choice['.$zone->id.']', 'pattern:'.$pattern->id, false, ['class' => 'form-check-input']) !!}
                                <label class="form-check-label">Pattern: {{ $pattern->name }}</label>
                            </div>
                        @endforeach

                        @if ($zone->allow_free_color)
                            <div class="form-check">
                                {!! Form::radio('zone_choice['.$zone->id.']', 'free', false, ['class' => 'form-check-input']) !!}
                                <label class="form-check-label">Custom color</label>
                                {!! Form::text('zone_free_color['.$zone->id.']', null, ['class' => 'form-control form-control-sm mt-1', 'placeholder' => 'b8794a']) !!}
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach

            <p class="text-muted small">This action is not reversible.</p>
            <div class="text-right">
                {!! Form::button('Redeem', ['name' => 'action', 'value' => 'act', 'type' => 'submit', 'class' => 'btn btn-primary']) !!}
            </div>
        </div>
    </li>
@endif
