@php
    $tagBadges = $model->configuredTags();
@endphp
@if ($tagBadges->count())
    <div class="tag-badges mb-2">
        @foreach ($tagBadges as $tag)
            <span class="badge badge-secondary mr-1">{{ $tag->name }}</span>
        @endforeach
    </div>
@endif
