@extends('layouts.app')

@section('title') Site Sales @endsection

@section('content')
{!! breadcrumbs(['Site Sales' => 'sales']) !!}
<h1>Site Sales</h1>
@if(count($saleses))
    {!! $saleses->render('layouts._pagination') !!}
    @foreach($saleses as $sales)
        @include('sales._sales', ['sales' => $sales, 'page' => FALSE])
    @endforeach
    {!! $saleses->render('layouts._pagination') !!}
@else
    <div>No sales posts yet.</div>
@endif
@endsection
