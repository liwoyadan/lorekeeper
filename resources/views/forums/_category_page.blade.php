@if ($forums->count())
    <div class="card mb-3">
        <div class="card-body py-2">
            @foreach ($forums as $forum)
                <div class="row no-gutters align-items-center {{ $loop->last ? '' : 'border-bottom' }} py-2">
                    <div class="col-12 col-sm-5 col-xl-6">
                        <h5 class="mb-0">
                            {!! $forum->displayIcon(25) !!}
                            {!! $forum->displayName !!}
                        </h5>
                        <div class="text-muted" style="font-size: 0.9em;">
                            {!! $forum->description !!}
                        </div>
                        @if ($forum->accessibleSubforums->count())
                            <div class="small">
                                <b>Sub-Forums:</b>
                                {!! implode(', ', $forum->accessibleSubforums->pluck('displayName', 'id')->toArray()) !!}
                            </div>
                        @endif
                    </div>
                    <div class="col-3 col-sm-4 col-xl-3 text-center">
                        {!! $forum->comments->whereNull('child_id')->count() !!} Topics
                    </div>
                    <div class="col pl-2 pl-sm-0">
                        @if ($forum->comments->count())
                            <div class="text-truncate font-weight-bold">
                                {!! $forum->comments->sortByDesc('id')->first()->displayName !!}
                            </div>
                            <div class="small d-md-inline-block">
                                {!! pretty_date($forum->comments->sortByDesc('id')->first()->updated_at) !!} by {!! $forum->comments->sortByDesc('id')->first()->commenter->displayName !!}
                            </div>
                        @else
                            <div class="text-muted">
                                No Topics Yet.
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
