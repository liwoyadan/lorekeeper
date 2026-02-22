@php
    $characters = \App\Models\Character\Character::visible(Auth::check() ? Auth::user() : null)
        ->myo(0)
        ->whereNotIn(
            'id',
            $character->links
                ->pluck('character_1_id')
                ->merge($character->links->pluck('character_2_id'))
                ->toArray(),
        )
        ->where('id', '!=', $character->id)
        ->orderBy('slug', 'DESC')
        ->get()
        ->pluck('fullName', 'slug')
        ->toArray();
@endphp

<div id="characterComponents">
    <div class="submission-character mb-2 card">
        <div class="card-body">
            <div class="row no-gutters">
                <div class="col-md-3 pr-md-1 align-items-center justify-content-center d-flex">
                    <div class="d-flex text-center align-items-center">
                        <div class="character-image-blank">
                            Select character code.
                        </div>
                        <div class="character-image-loaded hide"></div>
                    </div>
                </div>

                <div class="col-md-9 pl-md-1">
                    <div class="form-group font-weight-bold">
                        {!! Form::label('slug', 'Character Code') !!}
                        {!! Form::select('slug', $characters, null, ['class' => 'form-control character-code selectize', 'placeholder' => 'Select Character']) !!}
                    </div>

                    @if (Auth::check() && Auth::user()->hasPower('manage_characters') && Auth::user()->id != $character->user_id)
                        <div class="alert alert-warning">
                            <b>You are establishing this link as a staff member.</b> Therefore you do not need to use an item to establish this link and it will be automatically approved.
                        </div>
                    @else
                        <div class="form-group font-weight-bold">
                            {!! Form::label('link_item_id', 'Link Item') !!}
                            {!! Form::select('link_item_id', $linkItems, null, ['class' => 'form-control selectize', 'placeholder' => 'Select an Item']) !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
