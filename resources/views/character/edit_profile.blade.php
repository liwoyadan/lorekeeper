@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    Editing {{ $character->fullName }}'s Profile
@endsection

@section('profile-content')
    @if ($character->is_myo_slot)
        {!! breadcrumbs(['MYO Slot Masterlist' => 'myos', $character->fullName => $character->url, 'Editing Profile' => $character->url . '/profile/edit']) !!}
    @else
        {!! breadcrumbs([
            $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
            $character->fullName => $character->url,
            'Editing Profile' => $character->url . '/profile/edit',
        ]) !!}
    @endif

    @include('character._header', ['character' => $character])

    @if ($character->user_id != Auth::user()->id)
        <div class="alert alert-warning">
            You are editing this character as a staff member.
        </div>
    @endif

    {{ html()->form('POST', $character->url . '/profile/edit')->open() }}
    @if (!$character->is_myo_slot)
        <div class="form-group">
            {{ html()->label('Name', 'name') }}
            {{ html()->text('name', $character->name)->class('form-control') }}
        </div>
        @if (config('lorekeeper.extensions.character_TH_profile_link'))
            <div class="form-group">
                {{ html()->label('Profile Link', 'link') }}
                {{ html()->text('link', $character->profile->link)->class('form-control') }}
            </div>
        @endif
    @endif
    <div class="form-group">
        {{ html()->label('Profile Content', 'text') }}
        {{ html()->textarea('text', $character->profile->text)->class('wysiwyg form-control') }}
    </div>

    @if ($character->user_id == Auth::user()->id)
        @if (!$character->is_myo_slot)
            <div class="row">
                <div class="col-md form-group">
                    {{ html()->label('Allow Gift Art', 'is_gift_art_allowed')->class('form-check-label mb-3') }} {!! add_help('This will place the character on the list of characters that can be drawn for gift art. This does not have any other functionality, but allow users looking for characters to draw to find your character easily.') !!}
                    {{ html()->select('is_gift_art_allowed', [0 => 'No', 1 => 'Yes', 2 => 'Ask First'], $character->is_gift_art_allowed)->class('form-control user-select') }}
                </div>
                <div class="col-md form-group">
                    {{ html()->label('Allow Gift Writing', 'is_gift_writing_allowed')->class('form-check-label mb-3') }} {!! add_help(
                        'This will place the character on the list of characters that can be written about for gift writing. This does not have any other functionality, but allow users looking for characters to write about to find your character easily.',
                    ) !!}
                    {{ html()->select('is_gift_writing_allowed', [0 => 'No', 1 => 'Yes', 2 => 'Ask First'], $character->is_gift_writing_allowed)->class('form-control user-select') }}
                </div>
            </div>
        @endif
        @if ($character->is_tradeable || $character->is_sellable)
            <div class="form-group disabled">
                {{ html()->checkbox('is_trading', $character->is_trading, 1)->class('form-check-input')->data('toggle', 'toggle') }}
                {{ html()->label('Up For Trade', 'is_trading')->class('form-check-label ml-3') }} {!! add_help('This will place the character on the list of characters that are currently up for trade. This does not have any other functionality, but allow users looking for trades to find your character easily.') !!}
            </div>
        @else
            <div class="alert alert-secondary">Cannot be set to "Up for Trade" as character cannot be traded or sold.</div>
        @endif
    @endif
    @if ($character->user_id != Auth::user()->id)
        <div class="form-group">
            {{ html()->checkbox('alert_user', true, 1)->class('form-check-input')->data('toggle', 'toggle')->data('onstyle', 'danger') }}
            {{ html()->label('Notify User', 'alert_user')->class('form-check-label ml-3') }} {!! add_help('This will send a notification to the user that their character profile has been edited. A notification will not be sent if the character is not visible.') !!}
        </div>
    @endif
    <div class="text-right">
        {{ html()->submit('Edit Profile')->class('btn btn-primary') }}
    </div>
    {{ html()->form()->close() }}

@endsection

@section('scripts')
    @parent
    @include('js._tinymce_wysiwyg')
@endsection
