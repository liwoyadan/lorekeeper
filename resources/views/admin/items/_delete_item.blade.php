@if ($item)
    {{ html()->form('POST', 'admin/data/items/delete/' . $item->id)->open() }}

    <p>You are about to delete the item <strong>{{ $item->name }}</strong>. This is not reversible. If this item exists in at least one user's possession, you will not be able to delete this item.</p>
    <p>Are you sure you want to delete <strong>{{ $item->name }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Item')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid item selected.
@endif
