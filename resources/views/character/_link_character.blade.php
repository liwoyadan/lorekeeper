<div class="col-xl-6 mb-xl-0 mt-xl-0">
    <div class="row no-gutters align-items-center justify-content-center">
        <div class="col-12 col-xl-auto {{ $direction == 'left' ? 'order-1' : 'order-2 order-xl-2' }}" style="max-width: 175px;">
            <div class="text-center">
                <div>
                    <a href="{{ $character->url }}">
                        <img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" />
                    </a>
                </div>
                <div class="mt-1">
                    <a href="{{ $character->url }}" class="h5 mb-0">
                        @if (!$character->is_visible)
                            <i class="fas fa-eye-slash"></i>
                        @endif
                        {{ $character->fullName }}
                    </a>
                    <div class="small">
                        Owned by {!! $character->displayOwner !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md {{ $direction == 'left' ? 'order-2 pt-1 pl-xl-1' : 'order-1 order-xl-1 pt-2 pb-4 pr-xl-1' }} py-xl-0">
            <div class="thought {{ $direction }} position-relative">
                <div class="thought-label {{ $direction }}">
                    {{ $character->name ?? $character->slug }} thinks...
                </div>
                <div class="flourish {{ $direction }}"></div>
                <div class="thought-content py-1">
                    @if ($link->getRelationshipInfo($character->id))
                        <span>
                            {!! $link->getRelationshipInfo($character->id) !!}
                        </span>
                    @else
                        <span class="faded font-italic">
                            {{ $character->name ?? $character->slug }} has not provided any thoughts on this link yet.
                        </span>
                    @endif
                </div>
                <div class="thought-type {{ $direction }}">
                    ...and considers {{ $link->getOtherCharacter($character->id)->name ?? $link->getOtherCharacter($character->id)->slug }} <span class="font-italic">{{ $link->getRelationType($character->id) }}</span>.
                </div>

                @if (Auth::check() && Auth::user()->id == $character->user_id)
                    <div class="text-right position-absolute" style="top: 0.5rem; {{ $direction == 'left' ? 'right: 0.5rem;' : 'left: 0.5rem;' }}">
                        <a href="#" class="faded edit-link-btn" data-id="{{ $link->id }}" data-slug="{{ $character->slug }}">
                            <i class="fas fa-edit" data-toggle="tooltip" title="Edit Link"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
