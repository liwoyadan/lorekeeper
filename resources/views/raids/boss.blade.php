@extends('raids.layout')

@section('raids-title')
    {!! $boss->name !!}
@endsection

@section('content')
    {!! breadcrumbs(['Raids Bosses' => 'raids/bosses', $boss->name => 'raids/boss/' . $boss->id]) !!}

    <div class="row no-gutters justify-content-center">
        <div class="col-md-11">
            @include('raids._raid_boss_entry', ['raid' => $boss])
        </div>
    </div>
@endsection
