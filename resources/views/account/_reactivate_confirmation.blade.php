@if (Auth::user()->is_deactivated)
    <p>This will reactivate your account, allowing you to use the site features again. Are you sure you want to do this?</p>
    {{ html()->form('POST', 'reactivate')->open() }}
    <div class="text-right">
        {{ html()->submit('Reactivate')->class('btn btn-success') }}
    </div>
    {{ html()->form()->close() }}
@else
    <p>Your account is not deactivated.</p>
@endif
