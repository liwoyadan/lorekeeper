@extends('admin.layout')

@section('admin-title')
    Forums
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Forums' => 'admin/forums']) !!}

    <h1 class="mb-1">Forums</h1>
    <div class="mb-2 text-right">
        <a class="btn btn-primary" href="{{ url('admin/forums/create') }}">
            <i class="fas fa-plus"></i> Create Forum
        </a>
    </div>

    <p>
        Here you can create forums for users to create threads in. Forums <b>without a parent</b> are treated as <b>categories</b>, and will not display on the forum index unless other forums are created using it as a parent.
    </p>

    @if (!count($forums))
        <p>No forums or forum categories found.</p>
    @else
        {!! $forums->render() !!}
        <div class="row no-gutters flex-wrap font-weight-bold pb-1 ubt-bottom">
            <div class="col-6 col-md-4">Name</div>
            <div class="col-6 col-md-5">Children</div>
            <div class="col-12 col-md-3">Last Edited</div>
        </div>
        @foreach ($forums as $forum)
            <div class="row no-gutters flex-wrap align-items-center py-1 {{ !$loop->first ? 'ubt-top' : '' }}">
                <div class="col-6 col-md-4 font-weight-bold">
                    <div class="logs-table-cell ubt-texthide">
                        {!! $forum->displayName !!}
                    </div>
                </div>
                <div class="col-6 col-md-5">
                    <div class="logs-table-cell">
                        @if ($forum->children->count())
                            <small>
                                @foreach ($forum->children as $child)
                                    {!! $child->displayName !!} {!! $loop->last ? '' : ',' !!}
                                @endforeach
                            </small>
                        @else
                            <small>N/A</small>
                        @endif
                    </div>
                </div>
                <div class="col col-md-2">
                    <div class="logs-table-cell">
                        {!! pretty_date($forum->updated_at) !!}
                    </div>
                </div>
                <div class="col-auto col-md-1 text-right">
                    <a href="{{ url('admin/forums/edit/' . $forum->id) }}" class="btn btn-primary btn-sm">Edit</a>
                </div>
            </div>
        @endforeach
        {!! $forums->render() !!}

        <div class="text-center mt-4 small text-muted">{{ $forums->total() }} result{{ $forums->total() == 1 ? '' : 's' }} found.</div>
    @endif
@endsection
