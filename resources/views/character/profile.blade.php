@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    {{ $character->fullName }}'s Profile
@endsection

@section('meta-img')
    {{ $character->image->thumbnailUrl }}
@endsection

@section('profile-content')
    @if ($character->is_myo_slot)
        {!! breadcrumbs(['MYO Slot Masterlist' => 'myos', $character->fullName => $character->url, 'Profile' => $character->url . '/profile']) !!}
    @else
        {!! breadcrumbs([
            $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
            $character->fullName => $character->url,
            'Profile' => $character->url . '/profile',
        ]) !!}
    @endif

    @include('character._header', ['character' => $character])

    <div class="mb-3">
        <div class="text-center">
            <a href="{{ $character->image->canViewFull(Auth::check() ? Auth::user() : null) && file_exists(public_path($character->image->imageDirectory . '/' . $character->image->fullsizeFileName)) ? $character->image->fullsizeUrl : $character->image->imageUrl }}"
                data-lightbox="entry" data-title="{{ $character->fullName }}">
                <img src="{{ $character->image->canViewFull(Auth::check() ? Auth::user() : null) && file_exists(public_path($character->image->imageDirectory . '/' . $character->image->fullsizeFileName)) ? $character->image->fullsizeUrl : $character->image->imageUrl }}"
                    class="image img-fluid" alt="{{ $character->fullName }}" />
            </a>
        </div>
        @if ($character->image->canViewFull(Auth::check() ? Auth::user() : null) && file_exists(public_path($character->image->imageDirectory . '/' . $character->image->fullsizeFileName)))
            <div class="text-right">You are viewing the full-size image. <a href="{{ $character->image->imageUrl }}">View watermarked image</a>?</div>
        @endif
    </div>

    {{-- Bio --}}
    <a class="float-left" href="{{ url('reports/new?url=') . $character->url . '/profile' }}"><i class="fas fa-exclamation-triangle" data-toggle="tooltip" title="Click here to report this character's profile." style="opacity: 50%;"></i></a>
    @if (Auth::check() && ($character->user_id == Auth::user()->id || Auth::user()->hasPower('manage_characters')))
        <div class="text-right mb-2">
            <a href="{{ $character->url . '/profile/edit' }}" class="btn btn-outline-info btn-sm"><i class="fas fa-cog"></i> Edit Profile</a>
        </div>
    @endif
    @if ($character->profile->parsed_text)
        <div class="card mb-3">
            <div class="card-body parsed-text">
                {!! $character->profile->parsed_text !!}
            </div>
        </div>
    @endif

    @if ($character->is_trading || $character->is_gift_art_allowed || $character->is_gift_writing_allowed || $character->is_links_open)
        <div class="card mb-3">
            <ul class="list-group list-group-flush">
                @if ($character->is_gift_art_allowed >= 1 && !$character->is_myo_slot)
                    <li class="list-group-item">
                        <h5 class="mb-0"><i class="{{ $character->is_gift_art_allowed == 1 ? 'text-success' : 'text-secondary' }} far fa-circle fa-fw mr-2"></i>
                            {{ $character->is_gift_art_allowed == 1 ? 'Gift art is allowed' : 'Please ask before gift art' }}</h5>
                    </li>
                @endif
                @if ($character->is_gift_writing_allowed >= 1 && !$character->is_myo_slot)
                    <li class="list-group-item">
                        <h5 class="mb-0"><i class="{{ $character->is_gift_writing_allowed == 1 ? 'text-success' : 'text-secondary' }} far fa-circle fa-fw mr-2"></i>
                            {{ $character->is_gift_writing_allowed == 1 ? 'Gift writing is allowed' : 'Please ask before gift writing' }}</h5>
                    </li>
                @endif
                @if ($character->is_trading)
                    <li class="list-group-item">
                        <h5 class="mb-0"><i class="text-success far fa-circle fa-fw mr-2"></i> Open for trades</h5>
                    </li>
                @endif
                @if ($character->is_links_open)
                    <li class="list-group-item">
                        <h5 class="mb-0"><i class="text-success far fa-circle fa-fw mr-2"></i> Open for {{ __('links.link') }} requests</h5>
                    </li>
                @endif
            </ul>
        </div>
    @endif

    @if (isset($featuredLinks) && $featuredLinks->count())
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-star text-warning mr-2"></i> Featured {{ ucfirst(__('links.links')) }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($featuredLinks as $link)
                        @php $other = $link->getOtherCharacter($character->id); @endphp
                        <div class="col-sm-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center">
                                        <a href="{{ $other->url }}" class="mr-2 flex-shrink-0">
                                            <img src="{{ $other->image->thumbnailUrl }}" class="img-thumbnail" style="width: 56px; height: 56px; object-fit: cover;" alt="{{ $other->fullName }}" />
                                        </a>
                                        <div class="min-width-0">
                                            <a href="{{ $other->url }}" class="d-block font-weight-bold small text-truncate">
                                                @if (!$other->is_visible)<i class="fas fa-eye-slash"></i>@endif
                                                {{ $other->fullName }}
                                            </a>
                                            <div class="x-small">
                                                <span class="badge badge-secondary">{{ $link->getRelationType($character->id) }}</span>
                                            </div>
                                            @if ($link->getRelationshipInfo($character->id))
                                                <div class="x-small mt-1 text-truncate">{!! strip_tags($link->getRelationshipInfo($character->id)) !!}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-right">
                    <a href="{{ $character->url }}/{{ __('links.links') }}" class="small">View all {{ __('links.links') }} &rarr;</a>
                </div>
            </div>
        </div>
    @endif
@endsection
