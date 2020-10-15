@extends('account.layout')

@section('account-title') Settings @endsection

@section('account-content')
{!! breadcrumbs(['My Account' => Auth::user()->url, 'Settings' => 'account/settings']) !!}

<h1>Settings</h1>

<h3>Profile</h3>

{!! Form::open(['url' => 'account/profile']) !!}
    <div class="form-group">
        {!! Form::label('text', 'Profile Text') !!}
        {!! Form::textarea('text', Auth::user()->profile->text, ['class' => 'form-control wysiwyg']) !!}
    </div>
    <div class="text-right">
        {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
    </div>
{!! Form::close() !!}


@if($user_enabled == 1 || (Auth::user()->isStaff && $user_enabled == 2))
<h3>Home Location <span class="text-muted">({{ ucfirst($location_interval) }})</span></h3>
    @if(Auth::user()->isStaff && $user_enabled == 2)
        <div class="alert alert-warning">You can edit this because you are a staff member. Normal users cannot edit their own locations freely.</div>
    @endif
    @if($char_enabled == 1) 
        <div class="alert alert-warning">Your characters will have the same home as you.</div>
    @endif
    @if(Auth::user()->canChangeLocation)
        {!! Form::open(['url' => 'account/location']) !!}
            <div class="form-group row">
                <label class="col-md-2 col-form-label">Location</label>
                <div class="col-md-9">
                {!! Form::select('location', [0=>'Choose a Location'] + $locations, isset(Auth::user()->home_id) ? Auth::user()->home_id : 0, ['class' => 'form-control selectize']) !!}
                </div>
                <div class="col-md text-right">
                    {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
                </div>
            </div>
        {!! Form::close() !!}
    @else
        <div class="alert alert-warning">
        <strong>You can't change your location right now.</strong>
        You last changed it on {!! format_date(Auth::user()->home_changed, false) !!}.
        Home locations can be changed {{ $location_interval }}.
        </div>
    @endif
@endif



<h3>Email Address</h3>

<p>Changing your email address will require you to re-verify your email address.</p>

{!! Form::open(['url' => 'account/email']) !!}
    <div class="form-group row">
        <label class="col-md-2 col-form-label">Email Address</label>
        <div class="col-md-10">
            {!! Form::text('email', Auth::user()->email, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="text-right">
        {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
    </div>
{!! Form::close() !!}

<h3>Change Password</h3>

{!! Form::open(['url' => 'account/password']) !!}
    <div class="form-group row">
        <label class="col-md-2 col-form-label">Old Password</label>
        <div class="col-md-10">
            {!! Form::password('old_password', ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-form-label">New Password</label>
        <div class="col-md-10">
            {!! Form::password('new_password', ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-form-label">Confirm New Password</label>
        <div class="col-md-10">
            {!! Form::password('new_password_confirmation', ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="text-right">
        {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
    </div>
{!! Form::close() !!}

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {
    $('.selectize').selectize();
});
    
</script>
@endsection