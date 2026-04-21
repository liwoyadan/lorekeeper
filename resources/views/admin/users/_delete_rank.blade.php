@if ($rank)
    {{ html()->form('POST', 'admin/users/ranks/delete/' . $rank->id)->open() }}

    <p>You are about to delete the rank <strong>{{ $rank->name }}</strong>. This is not reversible and you will only be able to delete it if there are no users with this rank.</p>
    <p>Are you sure you want to delete <strong>{{ $rank->name }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Rank')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid rank selected.
@endif
