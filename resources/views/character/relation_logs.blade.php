@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    {{ $character->fullName }}'s Relationship Logs
@endsection

@section('meta-img')
    {{ $character->image->thumbnailUrl }}
@endsection

@section('profile-content')
    {!! breadcrumbs([
        $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
        $character->fullName => $character->url,
        'Links' => $character->url . '/links',
        'Logs' => $character->url . '/relationship-logs',
    ]) !!}

    @include('character._header', ['character' => $character])

    <h3>Relationship Logs</h3>

    {!! $logs->render() !!}
    <div class="mb-4 logs-table">
        <div class="logs-table-header">
            <div class="row no-gutters">
                <div class="col-6 col-md-2">
                    <div class="logs-table-cell">Sender</div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="logs-table-cell">Recipient</div>
                </div>
                <div class="col-6 col-md-1">
                    <div class="logs-table-cell">
                        <span data-toggle="tooltip" title="Character One"><i class="fa-solid fa-user"></i> #1</span>
                    </div>
                </div>
                <div class="col-6 col-md-1">
                    <div class="logs-table-cell">
                        <span data-toggle="tooltip" title="Character Two"><i class="fa-solid fa-user"></i> #2</span>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="logs-table-cell">Log</div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="logs-table-cell">Date</div>
                </div>
            </div>
        </div>
        <div class="logs-table-body">
            @foreach ($logs as $log)
                <div class="logs-table-row">
                    @include('character._relation_log_row', ['log' => $log])
                </div>
            @endforeach
        </div>
    </div>
    {!! $logs->render() !!}
@endsection
