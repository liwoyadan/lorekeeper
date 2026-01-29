@if ($forum->has_image)
    <div class="text-center mb-2">
        <a href="{!! $forum->imageUrl !!}" data-lightbox="entry" data-title="{!! $forum->name !!}">
            <img src="{!! $forum->imageUrl !!}" class="img-fluid" />
        </a>
    </div>
@endif

@if (isset($forum->description))
    <div class="card card-body p-3 mb-2">
        {!! $forum->parsed_description !!}
    </div>
@endif
