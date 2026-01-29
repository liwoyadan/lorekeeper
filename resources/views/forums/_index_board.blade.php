<div class="row no-gutters align-items-center {{ $isLast ? '' : 'border-bottom' }} pr-3">
    <div class="col-auto align-self-stretch pr-2">
        <div class="h-100" style="background-color: {{ $board->color ?? 'transparent' }}; border-bottom-left-radius: {{ $isLast ? '.25rem' : '0px' }}; width: 10px;"></div>
    </div>

    <div class="col col-sm-5 col-xl py-2">
        <h5 class="mb-0">
            {!! $board->displayIcon(30) !!}
            {!! $board->displayName !!}
        </h5>
        <div class="text-muted" style="font-size: 0.9em;">
            {!! $board->description !!}
        </div>
        @if ($board->accessibleSubforums->count())
            <div class="small">
                <b>Sub-Forums:</b>
                {!! implode(', ', $forum->accessibleSubforums->pluck('displayName', 'id')->toArray()) !!}
            </div>
        @endif
    </div>

    <div class="col-3 col-sm-4 col-xl-3 text-center py-2">
        {!! $board->comments->whereNull('child_id')->count() !!} Topics
    </div>

    <div class="col-9 col-xl pl-2 pl-sm-0 py-2">
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
