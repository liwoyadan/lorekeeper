@if ($forums->count())
    <div class="card mb-2">
        <div class="card-body p-0">
            @foreach ($forums as $forum)
                <div class="position-relative {{ $loop->last ? '' : 'border-bottom' }} py-1 px-3">
                    @if (isset($forum->forum_styles['use_board_bg']) && $forum->forum_styles['use_board_bg'])
                        <div class="forum-heading-bg" style="background-image: url('{{ $forum->imageUrl }}'); opacity: {{ $forum->forum_styles['board_bg_opacity'] ?? '15' }}%;"></div>
                    @endif
                    <div class="row no-gutters align-items-center py-2">
                        <div class="col-12 col-sm-5 col-xl-6">
                            <h5 class="mb-0">
                                {!! $forum->displayIcon(25) !!}
                                {!! $forum->displayName !!}
                            </h5>
                            <div style="font-size: 0.9em; opacity: 0.8;">
                                {!! $forum->description !!}
                            </div>
                            @if ($forum->characters_enabled)
                                <div class="small">
                                    <i class="fas fa-paw" aria-hidden="true"></i> You can post as a character on this board!
                                </div>
                            @endif
                            @if ($forum->accessibleSubforums->count())
                                <div class="small">
                                    <b>Sub-Forums:</b>
                                    {!! implode(', ', $forum->accessibleSubforums->pluck('displayName', 'id')->toArray()) !!}
                                </div>
                            @endif
                        </div>
                        <div class="col-3 col-sm-4 col-xl-3 text-center small py-2">
                            <div>
                                <b>{!! $forum->comments->whereNull('child_id')->count() !!}</b> Topics
                            </div>
                            <div>
                                <b>{!! $forum->comments->count() !!}</b> Posts
                            </div>
                        </div>
                        <div class="col pl-2 pl-sm-0 text-right text-md-left">
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
                </div>
            @endforeach
        </div>
    </div>
@endif
