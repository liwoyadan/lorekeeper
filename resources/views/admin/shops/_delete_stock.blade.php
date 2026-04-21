@if ($stock)
    {{ html()->form('POST', 'admin/data/shops/stock/delete/' . $stock->id)->open() }}

    <p>You are about to delete the stock <strong>{{ $stock->item?->name ?? 'deleted ' . $stock->stock_type }}</strong>.</p>
    <p>Are you sure you want to delete <strong>{{ $stock->item->name ?? 'deleted ' . $stock->stock_type }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Stock')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid stock selected.
@endif
