@extends('admin.layout')

@section('admin-title')
    Raid Bosses
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Raid Bosses' => 'admin/data/raid-bosses']) !!}

    <h1>Raid Bosses</h1>

    <p>
        This is a list of all the raid bosses on the site. <b>Raid bosses must be created directly from a raid's page.</b>
    </p>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('raid_id', ['any' => 'Any Raid'] + $raids->pluck('name', 'id')->toArray(), Request::get('raid_id'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">{!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}</div>
        {!! Form::close() !!}
    </div>

    @if (!count($bosses))
        <p>No raid bosses found.</p>
    @else
        {!! $bosses->render() !!}
        <div class="mb-4 logs-table">
            <div class="logs-table-header">
                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="logs-table-cell">Name</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="logs-table-cell">Health</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="logs-table-cell">Raid</div>
                    </div>
                    <div class="col col-md-2">
                        <div class="logs-table-cell">Image Count</div>
                    </div>
                </div>
            </div>
            <div class="logs-table-body">
                @foreach ($bosses as $boss)
                    <div class="logs-table-row">
                        <div class="row flex-wrap">
                            <div class="col-12 col-md-3 text-truncate">
                                <div class="logs-table-cell">
                                    @if (!$boss->is_visible)
                                        <i class="fas fa-eye-slash" data-toggle="tooltip" title="This boss is currently not visible."></i>
                                    @endif
                                    {{ $boss->name }}
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="logs-table-cell">
                                    {{ $boss->remainingHealth }} /
                                    {{ $boss->health ?? 'No Limit' }}
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="logs-table-cell">
                                    {!! $boss->raid_id ? $boss->raid->displayName : '-' !!}
                                </div>
                            </div>
                            <div class="col col-md-2">
                                <div class="logs-table-cell">
                                    {!! $boss->images->count() !!}
                                </div>
                            </div>
                            <div class="col text-right">
                                <div class="logs-table-cell">
                                    <a href="{{ url('admin/data/raid-bosses/edit/' . $boss->id) }}" class="btn btn-primary py-0 px-2">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {!! $bosses->render() !!}

        <div class="text-center mt-4 small text-muted">{{ $bosses->total() }} result{{ $bosses->total() == 1 ? '' : 's' }} found.</div>
    @endif

@endsection

@section('scripts')
    @parent
@endsection
