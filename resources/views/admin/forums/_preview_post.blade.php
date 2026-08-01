<div class="{!! isset($borderDecor) && $borderDecor?->cssStyle ? '' : 'border' !!} rounded mb-3 row no-gutters position-relative" {!! isset($borderDecor) && $borderDecor?->cssStyle ? 'style="' . $borderDecor->cssStyle . '"' : '' !!}>
    @if (isset($bgDecor) && $bgDecor?->cssStyle)
        <div class="forum-heading-bg" style="{{ $bgDecor->cssStyle }}"></div>
    @endif
    <div class="col-md-2 text-center border-md-right border-bottom border-md-bottom-0 py-2">
        <h5 class="mb-1">
            @isset($flair)
                {!! $flair->previewFlair() !!}
            @else
                <a href="#" class="display-user"><i class="fas fa-user mr-1" style="opacity: 50%;"></i>Username</a>
            @endisset
        </h5>
        <div class="comment-avatar mb-1">
            <img class="mx-100 rounded-circle" src="{{ asset('images/avatars/default.jpg') }}" style="aspect-ratio: 1/1; max-height: 100px;" alt="Default Avatar">
        </div>
        <div>
            {!! $flair->displayFlair ?? '<span class="small text-muted">(No Forum Flair)</span>' !!}
        </div>
        <div>
            <span>
                <i class="fas fa-user mr-1" style="opacity: 50%;"></i>
                <strong>Member</strong>
            </span>
        </div>
        <div class="small mt-1">
            25 Posts
        </div>
    </div>

    <div class="col-md d-flex flex-column">
        <div class="border-bottom p-2">
            <div class="row no-gutters justify-content-between">
                <div class="col d-flex flex-wrap align-items-center">
                    <i class="fas fa-link mr-1" style="opacity: 50%;" data-toggle="tooltip" title="Comment Link"></i>
                    12/22/2025
                </div>
            </div>
        </div>
        <div class="p-2 flex-grow-1 d-flex flex-column">
            <div>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec volutpat porta ligula, eget hendrerit ante. Aenean eleifend justo eu nunc molestie, ac dictum magna malesuada. Sed ultrices justo sit amet nisl euismod, ac sodales
                    ligula maximus. Sed eu enim egestas, blandit leo eget, gravida nibh. Sed dapibus, eros nec dignissim consectetur, ex dolor faucibus enim, vitae luctus elit leo vitae nisi.
                </p>
                <p>
                    Quisque consectetur, odio sit amet placerat commodo, lacus ipsum gravida quam, at pulvinar lorem ante sed lorem. Mauris mollis velit arcu, ac condimentum lacus tristique nec. Proin sagittis felis ac ex tristique, vitae auctor
                    tellus ultricies. Mauris vitae tincidunt sem. Aliquam volutpat laoreet risus, non tempus ante pharetra sed.
                </p>
            </div>
        </div>
        @if (config('lorekeeper.forums.allow_signatures.enabled'))
            <div class="px-2 pb-2">
                <hr class="mx-auto my-1" style="width: 90%;">
                <div class="forum-signature" style="overflow: auto; max-height: {{ config('lorekeeper.forums.allow_signatures.max_height') ?? '' }}px;">
                    <div class="text-center">
                        This is an optional signature, only displayed when both enabled in config and set by the user.
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
