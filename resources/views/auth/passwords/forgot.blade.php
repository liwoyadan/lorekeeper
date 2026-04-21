@extends('layouts.app')

@section('title')
    Forgot Password
@endsection

@section('content')
    @if (isset($status) || (isset($errors) && count($errors)))
        <div class="alert alert-success mb-4">
            Form submitted successfully. If this email address is registered to an account, you will receive a password reset email.
        </div>
    @endif

    <h1>Forgot Password</h1>

    <p>Please enter the email address associated with your account. An email will be sent to this address to reset your password.</p>

    {{ html()->form('POST', url('forgot-password'))->open() }}
    <div class="form-group row">
        {{ html()->label('Email', 'email')->class('col-md-3 col-form-label text-md-right') }}
        <div class="col-md-7">
            {{ html()->text('email')->class('form-control') }}
        </div>
    </div>
    <div class="text-right">
        {{ html()->submit('Submit')->class('btn btn-primary') }}
    </div>
    {{ html()->form()->close() }}
@endsection
