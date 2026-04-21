@if ($shop)
    {{ html()->form('POST', 'admin/data/shops/delete/' . $shop->id)->open() }}

    <p>You are about to delete the shop <strong>{{ $shop->name }}</strong>. This is not reversible. If you would like to hide the shop from users, you can set it as inactive from the shop settings page.</p>
    <p>Are you sure you want to delete <strong>{{ $shop->name }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Shop')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid shop selected.
@endif
