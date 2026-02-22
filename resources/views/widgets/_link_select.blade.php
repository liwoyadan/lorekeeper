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

<div id="characterComponents" class="hide">
    <div class="submission-character mb-2 card">
        <div class="card-body">
            <div class="text-right">
                <a href="#" class="remove-character text-danger">
                    <i class="fas fa-times"></i>
                </a>
            </div>

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
                        {!! Form::label('slug[]', 'Character Code') !!}
                        {!! Form::select('slug[]', $characters, null, ['class' => 'form-control character-code', 'placeholder' => 'Select Character']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
