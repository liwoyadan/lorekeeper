@extends('admin.layout')

@section('admin-title')
    Gallery Currency Queue
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', ($currency ? $currency->name : 'Gallery Currency') . ' Queue' => 'admin/gallery/currency/pending']) !!}

    <h1>
        {!! $currency ? $currency->name : 'Gallery Currency' !!} Queue
    </h1>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ set_active('admin/gallery/currency/pending*') }} {{ set_active('admin/gallery/currency') }}" href="{{ url('admin/gallery/currency/pending') }}">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ set_active('admin/gallery/currency/valued*') }}" href="{{ url('admin/gallery/currency/valued') }}">Processed</a>
        </li>
    </ul>

    <div>
        {{ html()->form('GET')->class('form-inline justify-content-end')->open() }}
        <div class="form-group mr-sm-3 mb-3">
            {{ html()->select('gallery_id', $galleries, Request::get('gallery_id'))->class('form-control')->placeholder('Any Gallery') }}
        </div>
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {{ html()->select(
                    'sort',
                    [
                        'newest' => 'Newest First',
                        'oldest' => 'Oldest First',
                    ],
                    Request::get('sort') ?: 'oldest',
                )->class('form-control') }}
            </div>
            <div class="form-group ml-3 mb-3">
                {{ html()->submit('Search')->class('btn btn-primary') }}
            </div>
        </div>
        {{ html()->form()->close() }}
    </div>

    {!! $submissions->render() !!}

    @foreach ($submissions as $key => $submission)
        @include('galleries._queue_submission', ['queue' => true])
    @endforeach

    {!! $submissions->render() !!}
    <div class="text-center mt-4 small text-muted">{{ $submissions->total() }} result{{ $submissions->total() == 1 ? '' : 's' }} found.</div>
@endsection
