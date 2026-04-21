@extends('admin.layout')

@section('admin-title')
    Grant User Currency
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Grant Currency' => 'admin/grants/user-currency']) !!}

    <h1>Grant User Currency</h1>

    {{ html()->form('POST', 'admin/grants/user-currency')->open() }}

    <h3>Basic Information</h3>

    <div class="form-group">
        {{ html()->label('Username(s)', 'names[]') }} {!! add_help('You can select up to 10 users at once.') !!}
        {{ html()->select('names[]', $users, null)->id('usernameList')->class('form-control')->attribute('multiple', 'multiple') }}
    </div>

    <div class="row">
        <div class="col-md-6 form-group">
            {{ html()->label('Currency', 'currency_id') }}
            {{ html()->select('currency_id', $userCurrencies, null)->class('form-control') }}
        </div>
        <div class="col-md-6 form-group">
            {{ html()->label('Quantity', 'quantity') }} {!! add_help('If the value given is less than 0, this will be deducted from the user(s).') !!}
            {{ html()->text('quantity', null)->class('form-control') }}
        </div>
    </div>

    <div class="form-group">
        {{ html()->label('Reason (Optional)', 'data') }} {!! add_help('A reason for the grant. This will be noted in the logs.') !!}
        {{ html()->text('data', null)->class('form-control') }}
    </div>

    <div class="text-right">
        {{ html()->submit('Submit')->class('btn btn-primary') }}
    </div>

    {{ html()->form()->close() }}

    <script>
        $(document).ready(function() {
            $('#usernameList').selectize({
                maxItems: 10
            });
        });
    </script>
@endsection
