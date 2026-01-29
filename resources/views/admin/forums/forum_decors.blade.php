@extends('admin.layout')

@section('admin-title')
    Forum Decors
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Forums' => 'admin/forums', 'Forum Decors' => 'admin/forum-decors']) !!}

    <h1>Forum Decors</h1>

    <p>
        Forum decors are cosmetic decorations that can be displayed on a user's forum posts. Forum decor can either be set as default selectable by any user or can be granted as rewards.
    </p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/forum-decors/create') }}"><i class="fas fa-plus"></i> Create New Decor</a>
    </div>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('type', [], Request::get('type'), ['class' => 'form-control', 'placeholder' => 'Choose Type']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    @if (!count($decors))
        <p>No forum decors found.</p>
    @else
        {!! $decors->render() !!}
        <div class="mb-4 logs-table">
            <div class="logs-table-header">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="logs-table-cell">Name</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="logs-table-cell">Type</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="logs-table-cell">Image</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="logs-table-cell">Flags</div>
                    </div>
                    <div class="col-6 col-md-1">
                        <div class="logs-table-cell">Visible</div>
                    </div>
                </div>
            </div>
            <div class="logs-table-body">
                @foreach ($decors as $decor)
                    <div class="logs-table-row">
                        <div class="row flex-wrap">
                            <div class="col-12 col-md-4">
                                <div class="logs-table-cell">
                                    {{ $decor->name }}
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="logs-table-cell">
                                    @if ($decor->type)
                                        <span class="badge badge-secondary">{{ ucfirst($decor->type) }}</span>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="logs-table-cell">
                                    @if ($decor->has_image)
                                        <img src="{{ $decor->imageUrl }}" style="max-height: 30px; max-width: 50px;" />
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="logs-table-cell">
                                    @if ($decor->is_default)
                                        <span class="badge badge-primary">Default</span>
                                    @endif
                                    @if ($decor->staff_only)
                                        <span class="badge badge-danger">Staff</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6 col-md-1">
                                <div class="logs-table-cell">
                                    {!! $decor->is_visible ? '<i class="text-success fas fa-check"></i>' : '-' !!}
                                </div>
                            </div>
                            <div class="col-12 col-md-1 text-right">
                                <div class="logs-table-cell">
                                    <a href="{{ url('admin/forum-decors/edit/' . $decor->id) }}" class="btn btn-primary py-0 px-2">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        {!! $decors->render() !!}
    @endif
@endsection
