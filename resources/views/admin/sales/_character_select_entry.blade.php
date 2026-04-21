@php
    $characters = \App\Models\Character\Character::visible(Auth::user() ?? null)
        ->myo(0)
        ->orderBy('slug', 'DESC')
        ->get()
        ->pluck('fullName', 'slug')
        ->toArray();
@endphp

<div class="sales-character-entry mb-3 card">
    <div class="card-body">
        <div class="text-right"><a href="#" class="remove-character text-muted"><i class="fas fa-times"></i></a></div>
        <div class="row">
            <div class="col-md-2 align-items-stretch d-flex">
                <div class="d-flex text-center align-items-center">
                    <div class="character-image-blank hide">Enter character code.</div>
                    <div class="character-image-loaded">
                        @include('home._character', ['character' => $character->character])
                        {{ html()->hidden('image_id[]', $character->image_id) }}
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="form-group">
                    {{ html()->label('Character Code', 'slug[]') }}
                    {{ html()->select('slug[]', $characters, $character->character->slug)->class('form-control character-code')->placeholder('Select Character') }}
                </div>
                <div class="character-details">
                    <h4>Sale Details</h4>

                    <div class="form-group mb-2">
                        {{ html()->label('Type') }}
                        {{ html()->select('sale_type[]', ['flatsale' => 'Flatsale', 'auction' => 'Auction', 'ota' => 'OTA', 'xta' => 'XTA', 'raffle' => 'Raffle', 'flaffle' => 'Flatsale Raffle', 'pwyw' => 'Pay What You Want'], $character->type)->class('form-control character-sale-type')->placeholder('Select Sale Type') }}
                    </div>

                    <div class="saleType">
                        <div class="mb-3 {{ $character->type == 'flatsale' || $character->type == 'flaffle' ? 'show' : 'hide' }} flatOptions">
                            <div class="form-group">
                                {{ html()->label('Price') }}
                                {{ html()->number('price[]', isset($character->data['price']) ? $character->data['price'] : null)->class('form-control')->placeholder('Enter a Cost') }}
                            </div>
                        </div>

                        <div class="mb-3 {{ $character->type == 'auction' ? 'show' : 'hide' }} auctionOptions">
                            <div class="form-group">
                                {{ html()->label('Starting Bid') }}
                                {{ html()->number('starting_bid[]', isset($character->data['starting_bid']) ? $character->data['starting_bid'] : null)->class('form-control')->placeholder('Enter a Starting Bid') }}
                            </div>
                            <div class="form-group">
                                {{ html()->label('Minimum Increment') }}
                                {{ html()->number('min_increment[]', isset($character->data['min_increment']) ? $character->data['min_increment'] : null)->class('form-control')->placeholder('Enter a Minimum Increment') }}
                            </div>
                        </div>

                        <div class="mb-3 {{ $character->type == 'auction' || $character->type == 'xta' || $character->type == 'ota' ? 'show' : 'hide' }} xtaOptions">
                            <div class="form-group">
                                {{ html()->label('Autobuy (Optional)') }}
                                {{ html()->number('autobuy[]', isset($character->data['autobuy']) ? $character->data['autobuy'] : null)->class('form-control')->placeholder('Enter an Autobuy') }}
                            </div>
                            <div class="form-group">
                                {{ html()->label('End Point (Optional)') }}
                                {{ html()->text('end_point[]', isset($character->data['end_point']) ? $character->data['end_point'] : null)->class('form-control')->placeholder('Provide information about when bids/offers close') }}
                            </div>
                        </div>

                        <div class="mb-3 {{ $character->type == 'pwyw' || $character->type == 'ota' || $character->type == 'xta' ? 'show' : 'hide' }} pwywOptions">
                            <div class="form-group">
                                {{ html()->label('Minimum Offer (Optional)') }}
                                {{ html()->number('minimum[]', isset($character->data['minimum']) ? $character->data['minimum'] : null)->class('form-control')->placeholder('Enter a Minimum') }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        {{ html()->label('Notes (Optional)') }}
                        {{ html()->text('description[]', $character->description)->class('form-control')->placeholder('Provide any additional notes necessary') }}
                    </div>

                    <div class="form-group mb-4">
                        {{ html()->label('Link (Optional)') }} {!! add_help('The URL for where to buy, bid, etc. on the character.') !!}
                        {{ html()->text('link[]', $character->link)->class('form-control')->placeholder('URL') }}
                    </div>

                    @if ($sales->characters->count() > 1)
                        <div class="form-group text-right">
                            {{ html()->checkbox('character_is_open[' . $character->character->slug . ']', $character->is_open, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
                            {{ html()->label('Is Open', 'character_is_open')->class('form-check-label ml-3') }} {!! add_help('Whether or not this particular character is open or available. If the sale post itself is closed, all character sales attached will also be displayed as closed.') !!}
                        </div>
                    @else
                        {{ html()->hidden('character_is_open[' . $character->character->slug . ']', 1) }}
                    @endif

                    {{ html()->hidden('new_entry[]', 0) }}
                </div>
            </div>
        </div>
    </div>
</div>
