<div class="col-md-6">
    <div class="row no-gutters align-items-center">
        <div class="col-auto {{ $direction == 'left' ? 'order-1' : 'order-1 order-md-2' }}">
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
                        @endif {{ $character->fullName }}
                    </a>
                    <div class="small">
                        Owned by {!! $character->displayOwner !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col {{ $direction == 'left' ? 'order-2' : 'order-2 order-md-1' }}">
            <div class="thought {{ $direction }}">
                <div class="thought-label {{ $direction }}">
                    {{ $character->name ?? $character->slug }} thinks...
                </div>
                <div class="flourish {{ $direction }}"></div>
                <div class="thought-content mt-3">
                    @if ($link->getRelationshipInfo($character->id))
                        {!! $link->getRelationshipInfo($character->id) !!}
                    @else
                        <span class="text-muted faded font-italic">
                            {{ $character->name ?? $character->slug }} has not provided any thoughts on this link yet.
                        </span>
                    @endif
                </div>
                <div class="thought-type {{ $direction }}">
                    ...and considers {{ $link->getOtherCharacter($character->id)->name ?? $link->getOtherCharacter($character->id)->slug }} <span class="font-italic">{{ $link->getRelationType($character->id) }}</span>.
                </div>
            </div>
        </div>
    </div>
</div>
