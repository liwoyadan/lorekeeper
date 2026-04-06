@php $other = $link->getOtherCharacter($character->id); @endphp
<div class="col-12 col-sm-6 mb-4 mb-sm-0 p-1 d-flex flex-column">
    <div class="text-center mb-2">
        <a href="{{ $character->url }}">
            <img src="{{ $character->image->thumbnailUrl }}" class="rounded border" style="width: 110px; height: 110px; object-fit: cover;" alt="{{ $character->fullName }}" />
        </a>
    </div>

    <div class="card h-100">
        <div class="card-body py-2 px-3 d-flex flex-column">
            <div class="text-center">
                <a href="{{ $character->url }}" class="font-weight-bold">
                    @if (!$character->is_visible)
                        <i class="fas fa-eye-slash"></i>
                    @endif
                    {{ $character->fullName }}
                </a>
                <div>
                    ...considers {{ $other->name ?? $other->slug }}
                    <span class="badge badge-primary" style="font-size: 0.9em;">{{ $link->getRelationType($character->id) }}</span>
                </div>
            </div>

            <hr class="my-2 w-100">

            <div style="max-height: 100px; overflow-y: auto;">
                <div>
                    @if ($link->getRelationshipInfo($character->id))
                        {!! $link->getRelationshipInfo($character->id) !!}
                    @else
                        <span class="font-italic text-muted">{{ $character->name ?? $character->slug }} has not provided any thoughts yet.</span>
                    @endif
                </div>
            </div>

            <div class="small text-muted text-right mt-auto">
                Owned by {!! $character->displayOwner !!}
                @if (Auth::check() && Auth::user()->id == $character->user_id)
                    <span class="ml-1">
                        <a href="#" class="text-primary feature-link-btn" data-id="{{ $link->id }}" data-slug="{{ $character->slug }}" data-toggle="tooltip"
                            title="{{ $link->isFeaturedForCharacter($character->id) ? 'Unfeature this ' . __('links.link') : 'Feature this ' . __('links.link') }}">
                            <i class="{{ $link->isFeaturedForCharacter($character->id) ? 'fas' : 'far' }} fa-star"></i>
                        </a>
                        <a href="#" class="text-muted edit-link-btn" data-id="{{ $link->id }}" data-slug="{{ $character->slug }}" data-toggle="tooltip" title="Edit {{ ucfirst(__('links.link')) }}">
                            <i class="fas fa-edit"></i>
                        </a>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
