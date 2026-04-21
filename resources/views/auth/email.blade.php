@extends('layouts.app')

@section('title')
    Add Email Address
@endsection

@section('content')
    <h1>Add Email Address</h1>
    <p>
        Your account does not have any email addresses linked to it. For the purposes of ensuring account security, you must link at your email address to your {{ config('lorekeeper.settings.site_name', 'Lorekeeper') }}
        account.
        This will ensure that you can recover your account if you forget your password or if off-site providers are disabled.
    </p>


    {{ html()->form('POST', url('email'))->open() }}

    <div class="form-group row">
        {{ html()->label('Email Address', 'email')->class('col-md-4 col-form-label text-md-right') }}
        <div class="col-md-6">
            {{ html()->email('email', old('email'))->class('form-control' . ($errors->has('email') ? ' is-invalid' : ''))->required() }}
            @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-6 offset-md-4">
            {{ html()->submit('Add Email Address')->class('btn btn-primary') }}
        </div>
    </div>

    {{ html()->form()->close() }}
@endsection
