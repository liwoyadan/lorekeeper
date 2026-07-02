@extends('layouts.app')

@section('title')
    Admin{!! View::hasSection('admin-title') ? ' :: ' . trim(View::getSection('admin-title')) : '' !!}
@endsection

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5/lib/codemirror.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5/theme/dracula.css">
    <style>
        .CodeMirror { height: 250px; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/codemirror@5/lib/codemirror.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/codemirror@5/mode/css/css.js"></script>
@endpush

@section('sidebar')
    @include('admin._sidebar')
@endsection

@section('content')
    @yield('admin-content')
@endsection

@section('scripts')
    @parent
@endsection
