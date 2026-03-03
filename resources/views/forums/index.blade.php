@extends('layouts.app')

@section('title')
    Forum
@endsection

@section('content')
    {!! breadcrumbs(['Forum' => 'forum']) !!}

    <h1>Forums</h1>

    @if (count($forums))
        <div class="row no-gutters">
            <div class="col-md-9 pr-md-3">
                @foreach ($forums as $forum)
                    @if ($forum->children->where('is_active', 1)->count())
                        <div class="card mb-3">
                            <div class="card-header pt-0 px-0 position-relative">
                                @if (isset($forum->forum_styles['use_board_bg']) && $forum->forum_styles['use_board_bg'])
                                    <div class="forum-heading-bg rounded-top" style="background-image: url('{{ $forum->imageUrl }}'); opacity: {{ $forum->forum_styles['board_bg_opacity'] ?? '15' }}%;"></div>
                                @endif
                                <div class="forum-heading-content">
                                    @if (isset($forum->color) && $forum->color)
                                        <div class="w-100 rounded-top" style="background-color: {{ $forum->color }}; height: 5px;"></div>
                                    @endif
                                    <div class="row no-gutters justify-content-between align-items-center">
                                        <div class="col-auto">
                                            <h3 class="mb-0 px-3 mt-2">
                                                {!! $forum->displayName !!}
                                                <span class="small">
                                                    {!! (isset($forum->description) && $forum->description) ? add_help(strip_tags($forum->parsed_description)) : '' !!}
                                                </span>
                                            </h3>
                                        </div>
                                        
                                        <div class="col-auto px-3 pt-2">
                                            <a class="h4 mb-0 toggle-category" href="#forumCategory{{ $forum->id ?? '_' }}" data-toggle="collapse" aria-expanded="true">
                                                <i class="fas fa-angle-down" aria-hidden="true"></i>
                                                <span class="sr-only">Toggle Category: {{ $forum->name }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-0 collapse show" id="forumCategory{{ $forum->id ?? '_' }}">
                                @foreach ($forum->children()->staff(Auth::user() ?? null)->canAccess(Auth::user() ?? null)->orderBy('id')->orderBy('sort')->get() as $board)
                                    @if ($board->hasRestrictions && Auth::check() && Auth::user()->canVisitForum($board->id))
                                        @include('forums._index_board', ['board' => $board, 'isLast' => $loop->last])
                                    @elseif(!$board->hasRestrictions)
                                        @include('forums._index_board', ['board' => $board, 'isLast' => $loop->last])
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="col-md-3">
                @include('forums._recent_activity', ['recentPosts' => $recentPosts])
            </div>
        </div>
    @else
        <div class="text-center text-muted">
            No forums yet.
        </div>
    @endif
@endsection
