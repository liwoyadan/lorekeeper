@if ($currency)
    {{ html()->form('POST', 'admin/data/currencies/delete/' . $currency->id)->open() }}

    <p>You are about to delete the currency <strong>{{ $currency->name }}</strong>. This is not reversible. If users who possess this currency exist, their owned currency will also be deleted.</p>
    <p>Are you sure you want to delete <strong>{{ $currency->name }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Currency')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid currency selected.
@endif
