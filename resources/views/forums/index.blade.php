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
                            <div class="card-header pt-0 px-0">
                                @if (isset($forum->color) && $forum->color)
                                    <div class="w-100 rounded-top" style="background-color: {{ $forum->color }}; height: 10px;"></div>
                                @endif
                                <h3 class="mb-0 px-3 mt-2">
                                    {!! $forum->displayName !!}{!! (isset($forum->description) && $forum->description) ? add_help(strip_tags($forum->parsed_description)) : '' !!}
                                </h3>
                            </div>

                            <div class="card-body p-0">
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
