@if ($limit)
    {{ html()->form('POST', 'admin/data/limits/delete/' . $limit->id)->open() }}

    <p>You are about to delete the limit <strong>{{ $limit->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $limit->name }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Limit')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid limit selected.
@endif
