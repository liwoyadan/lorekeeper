<div class="card h-100">
    <div class="card-body p-2">
        <div class="row no-gutters align-items-center">
            <div class="col-2">
                <a href="{{ $other->url }}">
                    <img src="{{ $other->image->thumbnailUrl }}" class="img-thumbnail" style="width: 55px; height: 55px; object-fit: cover;" alt="{{ $other->fullName }}" />
                </a>
            </div>
            <div class="col-10 pl-2">
                <div class="border-bottom">
                    <a href="{{ $other->url }}" class="font-weight-bold small text-truncate">
                        @if (!$other->is_visible)
                            <i class="fas fa-eye-slash"></i>
                        @endif
                        {{ $other->fullName }}
                    </a>
                </div>

                <div class="row no-gutters">
                    <div class="col-auto pr-1">
                        <span class="badge badge-primary">{{ $link->getRelationType($character->id) }}</span>
                    </div>
                    <div class="col small text-truncate align-self-center">
                        @if ($link->getRelationshipInfo($character->id))
                            {!! strip_tags($link->getRelationshipInfo($character->id)) !!}
                        @else
                            <span class="text-muted">
                                {{ $character->name ?? $character->slug }} has not provided any thoughts yet.
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
