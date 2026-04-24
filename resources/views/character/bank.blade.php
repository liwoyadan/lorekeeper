@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    {{ $character->fullName }}'s Bank
@endsection

@section('profile-content')
    {!! breadcrumbs([
        $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
        $character->fullName => $character->url,
        'Bank' => $character->url . '/bank',
    ]) !!}

    @include('character._header', ['character' => $character])

    <h3>
        @if (Auth::check() && Auth::user()->hasPower('edit_inventories'))
            <a href="#" class="float-right btn btn-outline-info btn-sm" id="grantButton" data-toggle="modal" data-target="#grantModal"><i class="fas fa-cog"></i> Admin</a>
        @endif
        Currencies
    </h3>
    @if (count($currencies))
        <div class="card mb-4">
            <ul class="list-group list-group-flush">

                @foreach ($currencies as $currency)
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-6 text-right">
                                <strong>
                                    <a href="{{ $currency->url }}">
                                        {{ $currency->name }}
                                        @if ($currency->abbreviation)
                                            ({{ $currency->abbreviation }})
                                        @endif
                                    </a>
                                </strong>
                            </div>
                            <div class="col-lg-10 col-md-9 col-6">
                                {{ $currency->quantity }} @if ($currency->has_icon)
                                    {!! $currency->displayIcon !!}
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="card mb-4">
            <div class="card-body">
                No currencies owned.
            </div>
        </div>
    @endif

    @if (Auth::check() && Auth::user()->id == $character->user_id)
        <h3>
            Take/Give Currency
        </h3>
        {{ html()->form('POST', 'character/' . $character->slug . '/bank/transfer')->open() }}
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label>{{ html()->radio('action', true, 'take')->class('take-button') }} Take from Character</label>
                </div>
                <div class="col-md-6">
                    <label>{{ html()->radio('action', false, 'give')->class('give-button') }} Give to Character</label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    {{ html()->label('Quantity', 'quantity') }}
                    {{ html()->text('quantity', null)->class('form-control') }}
                </div>
                <div class="col-md-6 take">
                    {{ html()->label('Currency', 'currency_id') }}
                    {{ html()->select('take_currency_id', $takeCurrencyOptions, null)->class('form-control')->placeholder('Select Currency') }}
                </div>
                <div class="col-md-6 give hide">
                    {{ html()->label('Currency', 'currency_id') }}
                    {{ html()->select('give_currency_id', $giveCurrencyOptions, null)->class('form-control')->placeholder('Select Currency') }}
                </div>
            </div>
        </div>
        <div class="text-right">
            {{ html()->submit('Transfer')->class('btn btn-primary') }}
        </div>
        {{ html()->form()->close() }}
    @endif

    <h3>Latest Activity</h3>
    <div class="mb-4 logs-table">
        <div class="logs-table-header">
            <div class="row">
                <div class="col-6 col-md-2">
                    <div class="logs-table-cell">Sender</div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="logs-table-cell">Recipient</div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="logs-table-cell">Currency</div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="logs-table-cell">Log</div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="logs-table-cell">Date</div>
                </div>
            </div>
        </div>
        <div class="logs-table-body">
            @foreach ($logs as $log)
                <div class="logs-table-row">
                    @include('user._currency_log_row', ['log' => $log, 'owner' => $character])
                </div>
            @endforeach
        </div>
    </div>
    <div class="text-right">
        <a href="{{ url($character->url . '/currency-logs') }}">View all...</a>
    </div>

    @if (Auth::check() && Auth::user()->hasPower('edit_inventories'))
        <div class="modal fade" id="grantModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="modal-title h5 mb-0">[ADMIN] Grant/remove currency</span>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        {{ html()->form('POST', 'admin/character/' . $character->slug . '/grant')->open() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ html()->label('Currency', 'currency_id') }}
                                    {{ html()->select('currency_id', $currencyOptions, null)->class('form-control') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ html()->label('Quantity', 'quantity') }} {!! add_help('If the value given is less than 0, this will be deducted from the character.') !!}
                                    {{ html()->text('quantity', null)->class('form-control') }}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ html()->label('Reason (Optional)', 'data') }} {!! add_help('A reason for the grant. This will be noted in the logs.') !!}
                            {{ html()->text('data', null)->class('form-control') }}
                        </div>
                        <div class="text-right">
                            {{ html()->submit('Submit')->class('btn btn-primary') }}
                        </div>
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.take-button').on('click', function() {
                $('.take').removeClass('hide');
                $('.give').addClass('hide');
            })
            $('.give-button').on('click', function() {
                $('.give').removeClass('hide');
                $('.take').addClass('hide');
            })
        });
    </script>
@endsection
