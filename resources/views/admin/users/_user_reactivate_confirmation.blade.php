@if ($user->is_deactivated)
    <p>This will reactivate the user, allowing them to use the site features again. Are you sure you want to do this?</p>
    {{ html()->form('POST', 'admin/users/' . $user->name . '/reactivate')->open() }}
    <div class="text-right">
        {{ html()->submit('Reactivate')->class('btn btn-danger') }}
    </div>
    {{ html()->form()->close() }}
@else
    <p>This user is not deactivated.</p>
@endif
