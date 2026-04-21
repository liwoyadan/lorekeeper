@if ($sublist)
    {{ html()->form('POST', 'admin/data/sublists/delete/' . $sublist->id)->open() }}

    <p>You are about to delete the sublist <strong>{{ $sublist->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $sublist->name }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Sublist')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid sublist selected.
@endif
