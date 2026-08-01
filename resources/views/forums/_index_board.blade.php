<div class="position-relative">
    @if (isset($board->forum_styles['use_board_bg']) && $board->forum_styles['use_board_bg'])
        <div class="forum-heading-bg {{ $isLast ? '' : 'border-bottom' }}" style="background-image: url('{{ $board->imageUrl }}'); opacity: {{ $board->forum_styles['board_bg_opacity'] ?? '15' }}%;"></div>
    @endif
    <div class="row no-gutters align-items-center {{ $isLast ? '' : 'border-bottom' }} pr-2 forum-heading-content">
        <div class="col-auto align-self-stretch pr-2">
            <div class="h-100" style="background-color: {{ $board->color ?? 'transparent' }}; border-bottom-left-radius: {{ $isLast ? '.25rem' : '0px' }}; width: 5px;"></div>
        </div>
        <div class="col-11 col-md-5 col-xl py-2">
            <h5 class="mb-0">
                {!! $board->displayIcon(30) !!}
                {!! $board->displayName !!}
            </h5>
            <div style="font-size: 0.9em; opacity: 0.8;">
                {!! $board->description !!}
            </div>
            @if ($board->characters_enabled)
                <div class="small">
                    <i class="fas fa-paw" aria-hidden="true"></i> You can post as a character on this board!
                </div>
            @endif
            @if ($board->accessibleSubforums->count())
                <div class="small">
                    <b>Sub-Forums:</b>
                    {!! implode(', ', $board->accessibleSubforums->pluck('displayName', 'id')->toArray()) !!}
                </div>
            @endif
        </div>

        <div class="col-3 col-md-3 text-center py-2 small">
            <div>
                <b>{!! $board->comments->whereNull('child_id')->count() !!}</b> Topics
            </div>
            <div>
                <b>{!! $board->comments->count() !!}</b> Posts
            </div>
        </div>

        <div class="col-9 col-md-3 pl-2 pl-sm-0 pb-2 pt-sm-2 text-right text-md-left">
            @if ($board->comments->count())
                <div class="text-truncate font-weight-bold">
                    {!! $board->comments->sortByDesc('id')->first()->displayName !!}
                </div>
                <div class="small d-md-inline-block">
                    {!! pretty_date($board->comments->sortByDesc('id')->first()->updated_at) !!} by {!! $board->comments->sortByDesc('id')->first()->commenter->displayName !!}
                </div>
            @else
                <div class="text-muted">
                    No Topics Yet.
                </div>
            @endif
        </div>
    </div>
</div>
