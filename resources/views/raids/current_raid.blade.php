@extends('raids.layout')

@section('raids-title')
    Current Raid{!! $currentRaid ? ' - '.$currentRaid->name : '' !!}
@endsection

@section('content')
    {!! breadcrumbs(['Raids Index' => 'raids', 'Current Raid'. ($currentRaid ? ' - '.$currentRaid->name : '') => 'raids/current']) !!}

    <h1 class="text-center mb-0">
        Current Raid
    </h1>
    @if ($currentRaid)
        <h2 class="text-center mb-1">
            {!! $currentRaid->displayName !!}
        </h2>

        <div class="text-center mb-3">
            @include('widgets.raids._raid_boss_display', ['raid' => $currentRaid, 'raidBoss' => $currentRaid->currentBoss(), 'heading' => 'h3'])
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="h5 mb-1 text-center">
                    Started {!! $currentRaid->start_at ? pretty_date($currentRaid->start_at) : 'at an unknown time' !!} || Ends {!! $currentRaid->end_at ? format_date($currentRaid->end_at) : 'when boss is defeated' !!}
                </div>
                @if (Auth::check())
                    @include('raids._raid_attack', ['raid' => $currentRaid, 'boss' => $currentRaid->currentBoss()])
                @else
                    <div class="text-center">
                        You must be logged in to attack this boss.
                    </div>
                @endif
            </div>
        </div>

        <div class="card p-3">
            @include('raids._raid_rewards', ['rewards' => $currentRaid->rewards])
        </div>
    @endif
@endsection
