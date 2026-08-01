@extends('admin.layout')

@section('admin-title')
    Forum Flairs
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Forums' => 'admin/forums', 'Forum Flairs' => 'admin/forum-flairs']) !!}

    <h1>Forum Flairs</h1>

    <p>
        Forum flairs are cosmetic badges and styling applied to a given user's username on their forum posts, as well as displaying the flair itself underneath. Forum flairs can either be set as default selectable by any user, can be granted as rewards,
        or unlocked through forum post count requirements.
    </p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/forum-flairs/create') }}"><i class="fas fa-plus"></i> Create New Flair</a>
    </div>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    @if (!count($flairs))
        <p>No forum flairs found.</p>
    @else
        {!! $flairs->render() !!}
        <div class="mb-4 logs-table">
            <div class="logs-table-header">
                <div class="row">
                    <div class="col-6 col-md-4">
                        <div class="logs-table-cell">Name</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="logs-table-cell">Preview</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="logs-table-cell">Post Req.</div>
                    </div>
                </div>
            </div>
            <div class="logs-table-body">
                @foreach ($flairs as $flair)
                    <div class="logs-table-row">
                        <div class="row flex-wrap align-items-center">
                            <div class="col-6 col-md-4">
                                <div class="logs-table-cell">
                                    {{ $flair->name }}
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="logs-table-cell">
                                    {!! $flair->displayFlair !!}
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="logs-table-cell">
                                    {{ $flair->post_requirement ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="col-12 col-md text-right">
                                <div class="logs-table-cell">
                                    <a href="{{ url('admin/forum-flairs/edit/' . $flair->id) }}" class="btn btn-primary py-0 px-2">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        {!! $flairs->render() !!}
    @endif
@endsection
