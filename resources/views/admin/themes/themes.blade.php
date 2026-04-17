@extends('admin.layout')

@section('admin-title')
    Themes
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Themes' => 'admin/theme']) !!}

    <h1>Themes</h1>

    <p>You can create new Themes here for your users to be able to select from to view the site. </p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/themes/create') }}">
            <i class="fas fa-plus"></i> Create New Theme
        </a>
    </div>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mb-2">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group ml-2 mb-2">
            {!! Form::select(
                'sort',
                [
                    'alpha' => 'Sort Alphabetically (A-Z)',
                    'alpha-reverse' => 'Sort Alphabetically (Z-A)',
                    'newest' => 'Newest First',
                    'oldest' => 'Oldest First',
                ],
                Request::get('sort') ?: 'oldest',
                ['class' => 'form-control'],
            ) !!}
        </div>
        <div class="form-group ml-2 mb-2">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    @if (!count($siteThemes))
        <p>No themes found.</p>
    @else
        {!! $siteThemes->render() !!}
        <div class="row no-gutters">
            <div class="row flex-wrap col-12 pb-1 ubt-bottom font-weight-bold">
                <div class="col-12 col-md-5">Name</div>
                <div class="col-6 col-md">Creators</div>
            </div>
            @foreach ($siteThemes as $siteTheme)
                <div class="row flex-wrap col-12 mt-1 pt-2 ubt-top">
                    <div class="col-12 col-md-5">
                        {!! $siteTheme->is_default ? '<i class="fas fa-star mr-2" data-toggle="tooltip" title="This is the default theme."></i>' : '' !!}
                        {!! $siteTheme->is_active ? '' : '<i class="fas fa-eye-slash mr-2"></i>' !!}
                        {{ $siteTheme->name }}
                        <span class="small text-muted">
                            ({!! $siteTheme->userCount ? 'In use by ' . $siteTheme->userCount . ' user' . ($siteTheme->userCount == 1 ? '' : 's') : 'Not in Use' !!})
                        </span>
                    </div>
                    <div class="col-6 col-md">
                        {!! $siteTheme->creators ? $siteTheme->creatorDisplayName : 'N/A' !!}
                    </div>
                    <div class="col-6 col-md-1 text-right">
                        <a href="{{ url('admin/themes/edit/' . $siteTheme->id) }}" class="btn btn-primary py-0 px-2">Edit</a>
                    </div>
                </div>
            @endforeach
        </div>
        {!! $siteThemes->render() !!}
    @endif
@endsection
