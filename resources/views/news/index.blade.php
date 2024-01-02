@extends('layouts.app')

@section('title') Site News @endsection

@section('content')
{!! breadcrumbs(['Site News' => 'news']) !!}
<h1>Site News</h1>
@if(count($newses))
    {!! $newses->render('layouts._pagination') !!}
    @foreach($newses as $news)
        @include('news._news', ['news' => $news, 'page' => FALSE])
    @endforeach
    {!! $newses->render('layouts._pagination') !!}
@else
    <div>No news posts yet.</div>
@endif
@endsection
