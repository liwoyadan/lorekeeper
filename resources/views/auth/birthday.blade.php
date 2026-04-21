@extends('layouts.app')

@section('title')
    Add Your Birthday
@endsection

@section('content')
    <h1>Add Birthday</h1>
    <p>Your account does not have a birth date. To gain access to personalised site features, you must add a birthdate to your {{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} account. Your birthday is used to verify if you are allowed to
        access this site.
        <br>It is private by default.
    </p>
    <p><strong>Please make sure you enter the correct date.</strong></p>

    {{ html()->form('POST', url('/birthday'))->open() }}
    <div class="form-group row">
        {{ html()->label('Date of Birth', 'dob')->class('col-md-4 col-form-label text-md-right') }}
        <div class="col-md-6">
            <div class="col-md row">
                {{ html()->date('dob')->class('form-control') }}
            </div>
        </div>
        <div class="text-right">
            {{ html()->submit('Submit')->class('btn btn-primary') }}
        </div>
    </div>
    {{ html()->form()->close() }}
@endsection
