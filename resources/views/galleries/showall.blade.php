@extends('galleries.layout')

@section('gallery-title')
    All Recent Submissions
@endsection

@section('gallery-content')
    {!! breadcrumbs(['Gallery' => 'gallery', 'All Recent Submissions' => 'gallery/all']) !!}

    <h1>
        All Recent Submissions
    </h1>

    <p>This page displays all recent submissions, regardless of gallery.</p>
    @if (!$submissions->count())
        <p>There are no submissions.</p>
    @endif

    <div>
        {{ html()->form('GET')->class('form-inline justify-content-end')->open() }}
        <div class="form-group mr-3 mb-3">
            {{ html()->text('title', Request::get('title'))->class('form-control')->attribute('placeholder', 'Title') }}
        </div>
        <div class="form-group mr-3 mb-3">
            {{ html()->select('prompt_id', $prompts, Request::get('prompt_id'))->class('form-control') }}
        </div>
        <div class="form-group mr-3 mb-3">
            {{ html()->select('sort', ['newest' => 'Newest First', 'oldest' => 'Oldest First', 'alpha' => 'Sort Alphabetically (A-Z)', 'alpha-reverse' => 'Sort Alphabetically (Z-A)', 'prompt' => 'Sort by Prompt (Newest to Oldest)', 'prompt-reverse' => 'Sort by Prompt (Oldest to Newest)'], Request::get('sort') ?: 'category')->class('form-control') }}
        </div>
        <div class="form-group mb-3">
            {{ html()->submit('Search')->class('btn btn-primary') }}
        </div>
        {{ html()->form()->close() }}
    </div>

    @if ($submissions->count())
        {!! $submissions->render() !!}

        <div class="d-flex align-content-around flex-wrap mb-2">
            @foreach ($submissions as $submission)
                @include('galleries._thumb', ['submission' => $submission, 'gallery' => true])
            @endforeach
        </div>

        {!! $submissions->render() !!}
    @else
        <p>No submissions found!</p>
    @endif

@endsection
