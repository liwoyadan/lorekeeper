@php
    $characters = \App\Models\Character\Character::visible(Auth::user() ?? null)
        ->myo(0)
        ->orderBy('slug', 'DESC')
        ->get()
        ->pluck('fullName', 'slug')
        ->toArray();
@endphp

<div id="characterComponents" class="hide">
    <div class="sales-character mb-3 card">
        <div class="card-body">
            <div class="text-right"><a href="#" class="remove-character text-muted"><i class="fas fa-times"></i></a></div>
            <div class="row">
                <div class="col-md-2 align-items-stretch d-flex">
                    <div class="d-flex text-center align-items-center">
                        <div class="character-image-blank">Enter character code.</div>
                        <div class="character-image-loaded hide"></div>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="form-group">
                        {{ html()->label('Character Code', 'slug') }}
                        {{ html()->select('slug[]', $characters, null)->class('form-control character-code')->placeholder('Select Character') }}
                    </div>
                    <div class="character-details hide">
                        <h4>Sale Details</h4>

                        <div class="form-group mb-2">
                            {{ html()->label('Type') }}
                            {{ html()->select('sale_type[]', ['flatsale' => 'Flatsale', 'auction' => 'Auction', 'ota' => 'OTA', 'xta' => 'XTA', 'raffle' => 'Raffle', 'flaffle' => 'Flatsale Raffle', 'pwyw' => 'Pay What You Want'], null)->class('form-control character-sale-type')->placeholder('Select Sale Type') }}
                        </div>

                        <div class="saleType">
                            <div class="mb-3 hide flatOptions">
                                <div class="form-group">
                                    {{ html()->label('Price') }}
                                    {{ html()->number('price[]', null)->class('form-control')->placeholder('Enter a Cost') }}
                                </div>
                            </div>

                            <div class="mb-3 hide auctionOptions">
                                <div class="form-group">
                                    {{ html()->label('Starting Bid') }}
                                    {{ html()->number('starting_bid[]', null)->class('form-control')->placeholder('Enter a Starting Bid') }}
                                </div>
                                <div class="form-group">
                                    {{ html()->label('Minimum Increment') }}
                                    {{ html()->number('min_increment[]', null)->class('form-control')->placeholder('Enter a Minimum Increment') }}
                                </div>
                            </div>

                            <div class="mb-3 hide xtaOptions">
                                <div class="form-group">
                                    {{ html()->label('Autobuy (Optional)') }}
                                    {{ html()->number('autobuy[]', null)->class('form-control')->placeholder('Enter an Autobuy') }}
                                </div>
                                <div class="form-group">
                                    {{ html()->label('End Point (Optional)') }}
                                    {{ html()->text('end_point[]', null)->class('form-control')->placeholder('Provide information about when bids/offers close') }}
                                </div>
                            </div>

                            <div class="mb-3 hide pwywOptions">
                                <div class="form-group">
                                    {{ html()->label('Minimum Offer (Optional)') }}
                                    {{ html()->number('minimum[]', null)->class('form-control')->placeholder('Enter a Minimum') }}
                                </div>
                            </div>
                        </div>

                        <div class="form-group my-2">
                            {{ html()->label('Notes (Optional)') }}
                            {{ html()->text('description[]', null)->class('form-control')->placeholder('Provide any additional notes necessary') }}
                        </div>

                        <div class="form-group mb-4">
                            {{ html()->label('Link (Optional)') }} {!! add_help('The URL for where to buy, bid, etc. on the character.') !!}
                            {{ html()->text('link[]', null)->class('form-control')->placeholder('URL') }}
                        </div>

                        {{ html()->hidden('new_entry[]', 1) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
