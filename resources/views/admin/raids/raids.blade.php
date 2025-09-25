@extends('admin.layout')

@section('admin-title')
    Raids
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Raids' => 'admin/data/raids']) !!}

    <h1>Raids</h1>

    <p>
        This is a list of all the raids on the site.
    </p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/data/raids/create') }}">
            <i class="fas fa-plus"></i> Create New Raid
        </a>
    </div>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mb-3">{!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}</div>
        {!! Form::close() !!}
    </div>

    @if (!count($raids))
        <p>No raids found.</p>
    @else
        {!! $raids->render() !!}
        <div class="mb-4 logs-table">
            <div class="logs-table-header">
                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="logs-table-cell">Name</div>
                    </div>
                    <div class="col col-md-2">
                        <div class="logs-table-cell">
                            Active?
                            {!! add_help('This is <b>regardless of visibility</b>, whether or not the raid is active based on <b>start time</b> and <b>end time</b>.') !!}
                        </div>
                    </div>
                    <div class="col col-md-3">
                        <div class="logs-table-cell">Starts</div>
                    </div>
                    <div class="col col-md-3">
                        <div class="logs-table-cell">Ends</div>
                    </div>
                </div>
            </div>
            <div class="logs-table-body">
                @foreach ($raids as $raid)
                    <div class="logs-table-row">
                        <div class="row flex-wrap">
                            <div class="col-12 col-md-3 text-truncate">
                                <div class="logs-table-cell">
                                    @if (!$raid->is_visible)
                                        <i class="fas fa-eye-slash" data-toggle="tooltip" title="This raid is currently not visible."></i>
                                    @endif
                                    {{ $raid->name }}
                                </div>
                            </div>
                            <div class="col col-md-2">
                                <div class="logs-table-cell">
                                    @if ($raid->isActive)
                                        <i class="fas fa-check text-success" data-toggle="tooltip" title="This raid is currently active."></i>
                                    @else
                                        <i class="fas fa-times text-danger" data-toggle="tooltip" title="This raid is currently inactive."></i>
                                    @endif
                                </div>
                            </div>
                            <div class="col col-md-3">
                                <div class="logs-table-cell">
                                    {!! $raid->start_at ? pretty_date($raid->start_at) : '-' !!}
                                </div>
                            </div>
                            <div class="col col-md-3">
                                <div class="logs-table-cell">
                                    {!! $raid->end_at ? pretty_date($raid->end_at) : '-' !!}
                                </div>
                            </div>
                            <div class="col text-right">
                                <div class="logs-table-cell">
                                    <a href="{{ url('admin/data/raids/edit/' . $raid->id) }}" class="btn btn-primary py-0 px-2">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {!! $raids->render() !!}

        <div class="text-center mt-4 small text-muted">{{ $raids->total() }} result{{ $raids->total() == 1 ? '' : 's' }} found.</div>
    @endif

@endsection

@section('scripts')
    @parent
@endsection
