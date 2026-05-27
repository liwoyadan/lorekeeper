@extends('admin.layout')

@section('admin-title')
    Changelogs
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Changelogs' => 'admin/changelogs']) !!}

    <h1>Changelogs</h1>

    <p>This is a list of changelog entries logged by staff. Each entry can be associated with a specific subject (e.g. a particular trait or item) or left general for site-wide changes.</p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/changelogs/create') }}"><i class="fas fa-plus"></i> Create New Changelog</a>
    </div>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('text', Request::get('text'), ['class' => 'form-control', 'placeholder' => 'Search text']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('type', $types, Request::get('type'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    @if (!count($changelogs))
        <p>No changelogs found.</p>
    @else
        {!! $changelogs->render() !!}
        <div class="mb-4 logs-table">
            <div class="logs-table-header">
                <div class="row">
                    <div class="col-4 col-md-3">
                        <div class="logs-table-cell">Type</div>
                    </div>
                    <div class="col-md-4 d-none d-md-block">
                        <div class="logs-table-cell">Log</div>
                    </div>
                    <div class="col-3 col-md-2">
                        <div class="logs-table-cell">Staff</div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="logs-table-cell">Posted</div>
                    </div>
                    <div class="col-1 col-md-1">
                        <div class="logs-table-cell"></div>
                    </div>
                </div>
            </div>
            <div class="logs-table-body">
                @foreach ($changelogs as $changelog)
                    <div class="logs-table-row">
                        <div class="row flex-wrap">
                            <div class="col-4 col-md-3">
                                <div class="logs-table-cell">
                                    {{ $changelog->typeLabel }}
                                    @if ($changelog->type_id && \App\Models\Changelog::typeIsModel($changelog->type) && $changelog->subject)
                                        ({!! $changelog->subject->displayName ?? '#' . $changelog->subject->id !!})
                                    @endif
                                    @if ($changelog->staff_only)
                                        <span class="badge badge-danger ml-1">Staff Only</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 d-none d-md-block">
                                <div class="logs-table-cell">{!! $changelog->parsed_text !!}</div>
                            </div>
                            <div class="col-3 col-md-2">
                                <div class="logs-table-cell">{!! $changelog->staff ? $changelog->staff->displayName : '<em>Unknown</em>' !!}</div>
                            </div>
                            <div class="col-4 col-md-2">
                                <div class="logs-table-cell">{!! pretty_date($changelog->created_at) !!}</div>
                            </div>
                            <div class="col-1 col-md-1 text-right">
                                <div class="logs-table-cell">
                                    <a href="{{ url('admin/changelogs/edit/' . $changelog->id) }}" class="btn btn-primary py-0 px-2">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        {!! $changelogs->render() !!}
        <div class="text-center mt-4 small text-muted">{{ $changelogs->total() }} result{{ $changelogs->total() == 1 ? '' : 's' }} found.</div>
    @endif
@endsection
